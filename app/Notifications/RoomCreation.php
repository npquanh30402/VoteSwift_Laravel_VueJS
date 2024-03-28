<?php

namespace App\Notifications;

use App\Models\VotingRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomCreation extends Notification
{
    use Queueable;

    private VotingRoom $room;

    public function __construct(VotingRoom $room)
    {
        $this->room = $room;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
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
}
