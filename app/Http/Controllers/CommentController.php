<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Comment;
use App\Models\Ticket;

class CommentController extends Controller
{
    public function store(Request $request, $ticket_id)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $ticket = Ticket::findOrFail($ticket_id);

        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        try {
            $ticket->user->notify(new NewCommentNotification($comment));
        } catch (\Exception $e) {
            Log::warning('Could not send comment notification: ' . $e->getMessage());
            // Continue execution even if notification fails
        }

        return redirect()->route('tickets.show', $ticket_id)->with('success', 'Comentario agregado.');
    }
}
