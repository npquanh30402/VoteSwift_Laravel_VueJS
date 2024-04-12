<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    public $room;
    public $sender;
    public $receiver;
    public $invitationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($room, $sender, $receiver, $invitationUrl)
    {
        $this->room = $room;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->invitationUrl = $invitationUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Voting Room Invitation')
            ->line("Hi, {$this->receiver->username}!")
            ->line("User {$this->sender->username} has invited you to join the room {$this->room->id}. You can join the room by the link below:")
            ->action('Join', $this->invitationUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'room_id' => $this->room->id,
            'sender_id' => $this->sender->id,
            'sender_username' => $this->sender->username,
            'sender_avatar' => $this->sender->avatar,
//            'receiver_id' => $this->receiver->id,
//            'receiver_username' => $this->receiver->username,
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = \App\Models\Notification::find($this->id);
        $notification->data = json_decode($notification->data);

        return new BroadcastMessage($notification->toArray());
    }
}
