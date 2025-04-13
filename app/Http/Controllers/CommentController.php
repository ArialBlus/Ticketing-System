<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Comment;
use App\Models\Ticket;

class CommentController extends Controller
{
    public function store(Request $request, $ticket_id)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Usar eager loading para el ticket y su usuario
        $ticket = Ticket::with('user')->findOrFail($ticket_id);

        // Verificar permisos
        if (auth()->user()->hasRole('usuario') && $ticket->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para comentar en este ticket.');
        }

        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        try {
            // Solo enviar notificación si el comentario no es del creador del ticket
            if ($ticket->user_id !== auth()->id()) {
                $ticket->user->notify(new NewCommentNotification($comment));
            }
        } catch (\Exception $e) {
            \Log::warning('Could not send comment notification: ' . $e->getMessage());
            // Continue execution even if notification fails
        }

        // Limpiar caché del ticket después de agregar un comentario
        Cache::forget('ticket_' . $ticket_id);

        return redirect()->route('tickets.show', $ticket_id)->with('success', 'Comentario agregado.');
    }
}
