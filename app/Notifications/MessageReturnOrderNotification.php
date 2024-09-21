<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReturnOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $message;
    public $status;
    public $sender;
    public $listener;
    public function __construct($status, $sender, $listener)
    {
        $this->message = "Your return order is ".$status;
        $this->status = $status;
        $this->sender = $sender;
        $this->listener = $listener;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'status' => $this->status,
            'sender' => $this->sender,
            'listener' => $this->listener,
        ];
    }

    public function toBroadcast($notifiable){
        return new BroadcastMessage([
            'message' => $this->message,
            'status' => $this->status,
            'sender' => $this->sender,
            'listener' => $this->listener,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notification.' . $this->listener->id);
    }
}
