<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Ticket;

class TicketStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Especificar los canales de notificación.
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast']; // Correo, base de datos y en tiempo real
    }

    /**
     * Notificación por correo electrónico.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Estado de tu ticket actualizado')
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Tu ticket **"' . $this->ticket->title . '"** ha cambiado de estado a **' . $this->ticket->status->name . '**.')
            ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Gracias por utilizar nuestro sistema.');
    }

    /**
     * Notificación guardada en la base de datos.
     */
    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'status' => $this->ticket->status->name,
            'updated_at' => now(),
        ];
    }

    /**
     * Notificación en tiempo real (broadcast).
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'status' => $this->ticket->status->name,
            'updated_at' => now(),
        ]);
    }
}
