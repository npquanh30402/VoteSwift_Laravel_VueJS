<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class VotingChat implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $room;
    public $message;
    public $plain_message;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, VotingRoom $room, $message)
    {
        $this->user = $user;
        $this->room = $room;
        $this->message = $message;
        $this->plain_message = Crypt::decryptString($message->content);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('voting.chat.' . $this->room->id);

        return [$privateChannel];
    }
}
