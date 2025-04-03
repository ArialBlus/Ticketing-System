<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Comment;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo comentario en tu ticket')
            ->line('Han comentado en tu ticket "' . $this->comment->ticket->title . '".')
            ->action('Ver Ticket', url('/tickets/' . $this->comment->ticket->id))
            ->line('Ingresa al sistema para revisar.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->comment->ticket->id,
            'message' => $this->comment->message,
        ];
    }
}
