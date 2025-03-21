<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageRefillInventoryNotification extends Notification
{
    use Queueable;
    public $message;
    public $sender;
    public $listener;
    public function __construct($sender, $listener)
    {
        $this->message="Your inventory is refilled.";
        $this->sender=$sender;
        $this->listener=$listener;
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
            'sender' => $this->sender,
            'listener' => $this->listener,
        ];
    }

    public function toBroadcast($notifiable){
        return new BroadcastMessage([
            'message' => $this->message,
            'sender' => $this->sender,
            'listener' => $this->listener,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notification.' . $this->listener->id);
    }
}
