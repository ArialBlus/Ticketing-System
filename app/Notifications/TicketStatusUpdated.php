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
        \Log::info('Configurando mensaje de correo', [
            'to' => $notifiable->email,
            'subject' => 'Estado de tu ticket actualizado',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'new_status' => $this->ticket->status->name
        ]);

        try {
            $message = (new MailMessage)
                ->subject('Estado de tu ticket actualizado')
                ->greeting('Hola, ' . $notifiable->name)
                ->line('Tu ticket "' . $this->ticket->title . '" ha cambiado de estado a ' . $this->ticket->status->name . '.')
                ->action('Ver Ticket', url('/tickets/' . $this->ticket->id))
                ->line('Gracias por utilizar nuestro sistema.')
                ->line('Este es un correo automático, por favor no responder.');

            \Log::info('Mensaje de correo configurado exitosamente', [
                'ticket_id' => $this->ticket->id,
                'user_email' => $notifiable->email
            ]);

            return $message;
        } catch (\Exception $e) {
            \Log::error('Error al configurar el mensaje de correo', [
                'ticket_id' => $this->ticket->id,
                'user_email' => $notifiable->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Propagar el error
        }
    }

    /**
     * Notificación guardada en la base de datos.
     */
    public function toDatabase($notifiable)
    {
        \Log::info('Guardando notificación en base de datos', [
            'user_id' => $notifiable->id,
            'ticket_id' => $this->ticket->id,
            'new_status' => $this->ticket->status->name
        ]);

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
        \Log::info('Enviando notificación por broadcast', [
            'user_id' => $notifiable->id,
            'ticket_id' => $this->ticket->id,
            'new_status' => $this->ticket->status->name
        ]);

        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'status' => $this->ticket->status->name,
            'updated_at' => now(),
        ]);
    }
}
