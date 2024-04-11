<?php

namespace App\Notifications;

use App\Models\VotingRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomCreation extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    private VotingRoom $room;

    public function __construct(VotingRoom $room)
    {
        $this->room = $room;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have created a new voting room')
            ->action('Visit your room', route('room.dashboard', $this->room->id))
            ->line('Thank you for using our application!');
    }

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
