<?php

namespace App\Notifications;

use App\Models\VotingRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomPublish extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    private VotingRoom $room;

    public function __construct(VotingRoom $room)
    {
        $this->room = $room;
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
            ->line('You have published a new voting room')
            ->action('Visit your room', route('room.dashboard', $this->room->id))
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
            'room_id' => $this->room->id
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = \App\Models\Notification::find($this->id);

        return new BroadcastMessage($notification->toArray());
    }
}
