<?php

namespace App\Events;

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

class VotingProcess implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, VotingRoom $room)
    {
        $this->user = $user->only(['id', 'username', 'avatar']);
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('voting.' . $this->room->id);

        $presenceChannel = new PresenceChannel('voting.' . $this->room->id);

        return [$privateChannel, $presenceChannel];
    }
}
