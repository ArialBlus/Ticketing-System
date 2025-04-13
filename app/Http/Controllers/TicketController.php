<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Models\Status;
use App\Models\Comment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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
            // Soporte solo ve los tickets asignados a él
            $query->where('assigned_to', auth()->id());
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
        // llama a la función getAvailableTechnician
        $technician = $this->getAvailableTechnician();
        // if ($technician) {
        //     // Asignar el ticket al técnico encontrado
        //     $technician->notify(new TicketAssigned($ticket));
        // }


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
        // Intentar obtener el ticket del caché
        $cacheKey = 'ticket_' . $id;
        $ticket = Ticket::with(['user', 'category', 'status', 'assignedTo'])->findOrFail($id);

        $comments = Cache::remember("ticket_{$id}_comments", 300, function () use ($ticket) {
            return $ticket->comments()->with('user')->get();
        });

        // Verificar permisos

        if (auth()->user()->hasRole('usuario') && $ticket->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        $statuses = Status::all();
        return view('tickets.show', compact('ticket', 'statuses'));
    }

    //edit method
    public function edit($id)
    {
        $ticket = Ticket::with(['user', 'category', 'status', 'assignedTo'])->findOrFail($id);
        
        // Verificar permisos
        if (auth()->user()->hasRole('usuario') && $ticket->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }

        if (auth()->user()->hasRole('soporte') && $ticket->assigned_to !== auth()->id()) {
            abort(403, 'Solo puedes ver/editar tickets asignados a ti.');
        }

        // Para usuarios de soporte, solo permitir cambiar a "En Proceso" si está en "Abierto"
        if (auth()->user()->hasRole('soporte') && $ticket->status_id !== 1) { // 1 = Abierto
            abort(403, 'Solo puedes cambiar el estado a "En Proceso" si el ticket está en "Abierto".');
        }

        $categories = Category::all();
        $statuses = Status::all();
        return view('tickets.edit', compact('ticket', 'categories', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Iniciando actualización de ticket', ['ticket_id' => $id]);
            
            $ticket = Ticket::findOrFail($id);
            \Log::info('Ticket encontrado', ['ticket_data' => $ticket->toArray()]);
            
            // Limpiar caché del ticket después de actualizar
            Cache::forget('ticket_' . $id);

            // Verificar permisos
            if (auth()->user()->hasRole('usuario')) {
                if ($ticket->user_id !== auth()->id()) {
                    \Log::info('Acceso denegado - Usuario intentando editar ticket de otro');
                    return redirect()->back()->with('error', 'No tienes permiso para editar este ticket.');
                }
            }
            
            if (auth()->user()->hasRole('soporte') && $ticket->assigned_to !== auth()->id()) {
                \Log::info('Acceso denegado - Soporte intentando editar ticket no asignado');
                return redirect()->back()->with('error', 'Solo puedes editar tickets asignados a ti.');
            }

            // Para usuarios de soporte, solo permitir cambiar a "En Proceso" si está en "Abierto"
            if (auth()->user()->hasRole('soporte') && isset($request->status_id) && $request->status_id !== 2) { // 2 = En Proceso
                return redirect()->back()->with('error', 'Solo puedes cambiar el estado a "En Proceso".');
            }

            // Validar los datos
            \Log::info('Iniciando validación de datos', ['request_data' => $request->all()]);
            
            // Si no tiene permiso para gestionar tickets, no validar el status_id
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'priority' => 'required|in:low,medium,high',
            ];

            if (auth()->user()->can('gestionar tickets')) {
                $rules['status_id'] = 'required|exists:statuses,id';
            }

            $validated = $request->validate($rules, [
                'title.required' => 'El título es obligatorio.',
                'title.max' => 'El título no puede tener más de 255 caracteres.',
                'description.required' => 'La descripción es obligatoria.',
                'category_id.required' => 'Debe seleccionar una categoría.',
                'category_id.exists' => 'La categoría seleccionada no es válida.',
                'priority.required' => 'Debe seleccionar una prioridad.',
                'priority.in' => 'La prioridad seleccionada no es válida.',
                'status_id.required' => 'Debe seleccionar un estado.',
                'status_id.exists' => 'El estado seleccionado no es válido.',
            ]);

            \Log::info('Datos validados correctamente', ['validated_data' => $validated]);

            // Guardar los cambios
            \Log::info('Intentando actualizar ticket', ['ticket_id' => $id]);
            
            // Si no tiene permiso para gestionar tickets, mantener el estado actual
            if (!auth()->user()->can('gestionar tickets') && !isset($validated['status_id'])) {
                $validated['status_id'] = $ticket->status_id;
            }

            $ticket->update($validated);
            \Log::info('Ticket actualizado exitosamente', ['ticket_data' => $ticket->toArray()]);

            // Si el estado cambió, notificar al usuario
            if ($ticket->isDirty('status_id')) {
                \Log::info('Estado cambiado - Enviando notificación', [
                    'old_status' => $ticket->getOriginal('status_id'),
                    'new_status' => $ticket->status_id,
                    'user_email' => $ticket->user->email
                ]);

                try {
                    $ticket->user->notify(new TicketStatusUpdated($ticket));
                    \Log::info('Notificación enviada exitosamente', [
                        'ticket_id' => $ticket->id,
                        'user_email' => $ticket->user->email
                    ]);
                    
                    session()->flash('success', 'Estado actualizado y notificación enviada al usuario.');
                } catch (\Exception $e) {
                    \Log::error('Error enviando notificación', [
                        'ticket_id' => $ticket->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    session()->flash('warning', 'Estado actualizado, pero hubo un error al enviar la notificación.');
                }
            }

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket actualizado correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar ticket', [
                'ticket_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error al actualizar el ticket. Por favor, intenta nuevamente.');
        }
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
        // y si no tiene, asignar uno automáticamente
        if ($request->status_id == 2 && !$ticket->assigned_to) {
            $technician = $this->getAvailableTechnician();
            if ($technician) {
                $ticket->assigned_to = $technician->id;
            } else {
                return back()->with('error', 'No hay técnicos disponibles para asignar.');
            }
        }


        // Actualizar el estado del ticket
        $ticket->update(['status_id' => $request->status_id]);

        // Enviar notificación al creador del ticket
        try {
            $ticket->user->notify(new TicketStatusUpdated($ticket));
            \Log::info('Notificación enviada exitosamente', [
                'ticket_id' => $ticket->id,
                'user_email' => $ticket->user->email
            ]);
        } catch (\Exception $e) {
            \Log::error('Error enviando notificación', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return redirect()->route('tickets.show', $id)->with('success', 'Estado del ticket actualizado.');
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado correctamente.');
    }


    // logica del tecnico de soporte para asignar tickets
    private function getAvailableTechnician()
    {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'soporte');
        })
        ->withCount(['tickets' => function ($query) {
            $query->where('status_id', 2); // Solo tickets en proceso
        }])
        ->orderBy('tickets_count', 'asc')
        ->first();
    }

}
