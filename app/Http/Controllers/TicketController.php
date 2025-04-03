<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Models\Status;
use App\Models\Comment;

use App\Notifications\TicketStatusUpdated;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Ticket::with('user', 'category', 'status', 'assignedTo')->orderBy('created_at', 'desc');

        // Aplicar restricciones según el rol
        if (auth()->user()->hasRole('usuario')) {
            $query->where('user_id', auth()->id());
        } elseif (auth()->user()->hasRole('soporte')) {
            // Soporte ve todos los tickets
            $query;
        }
        // Admin ve todos los tickets por defecto

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->paginate(10)->withQueryString();
        $categories = Category::all();
        $statuses = Status::all();

        return view('tickets.index', compact('tickets', 'categories', 'statuses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
        ]);

        // Buscar un técnico con menos tickets en proceso
        $technician = User::whereHas('roles', function ($query) {
            $query->where('name', 'soporte');
        })
        ->withCount(['tickets' => function ($query) {
            $query->where('status_id', 2); // Estado "En Proceso"
        }])
        ->orderBy('tickets_count', 'asc')
        ->first();

        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'status_id' => 1, // Estado inicial: "Abierto"
            'priority' => $request->priority,
            'assigned_to' => $technician ? $technician->id : null,
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado correctamente.');
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'category', 'status', 'comments.user', 'assignedTo'])->findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->hasRole('usuario') && $ticket->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $statuses = Status::all();
        return view('tickets.show', compact('ticket', 'statuses'));
    }

    public function edit($id)
    {
        $ticket = Ticket::with(['user', 'category', 'status', 'assignedTo'])->findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->hasRole('usuario') && !auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para editar tickets.');
        }

        if (auth()->user()->hasRole('soporte') && $ticket->assigned_to !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Solo puedes editar tickets asignados a ti.');
        }

        $categories = Category::all();
        $statuses = Status::all();
        return view('tickets.edit', compact('ticket', 'categories', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->hasRole('usuario')) {
            abort(403, 'No tienes permiso para editar tickets.');
        }
        
        if (auth()->user()->hasRole('soporte')) {
            if ($ticket->assigned_to !== auth()->id()) {
                abort(403, 'Solo puedes editar tickets asignados a ti.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high',
            'status_id' => 'required|exists:statuses,id',
        ]);

        // Log request this
        \Log::info('Updating ticket', ['request' => $request->all()]);

        $oldStatus = $ticket->status_id;

        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->category_id = $request->category_id;
        $ticket->priority = $request->priority;
        $ticket->status_id = $request->status_id;

        //modify ticket/ check
        if ($ticket->save()) {
            \Log::info('Ticket updated successfully', ['ticket' => $ticket->toArray()]);
        } else {
            \Log::error('Failed to update ticket', ['ticket' => $ticket->toArray()]);
        }

        // Si el estado cambió, notificar al usuario
        if ($oldStatus != $request->status_id) {
            $ticket->user->notify(new TicketStatusUpdated($ticket));
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado correctamente.')->withInput();
        //return redirect()->route('tickets.show', $id)->with('success', 'Ticket actualizado correctamente.');
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Reglas de negocio: No se puede saltar estados
        if ($ticket->status_id == 1 && $request->status_id == 3) {
            return back()->with('error', 'No puedes pasar de "Abierto" a "Resuelto" directamente.');
        }

        // Si el ticket se marca como "En Proceso", asegurarse de que tenga un técnico asignado
        if ($request->status_id == 2 && !$ticket->assigned_to) {
            return back()->with('error', 'No puedes cambiar a "En Proceso" sin asignar un técnico.');
        }

        // Actualizar el estado del ticket
        $ticket->update(['status_id' => $request->status_id]);

        // Enviar notificación al creador del ticket
        $ticket->user->notify(new TicketStatusUpdated($ticket));

        return redirect()->route('tickets.show', $id)->with('success', 'Estado del ticket actualizado.');
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado correctamente.');
    }
}
