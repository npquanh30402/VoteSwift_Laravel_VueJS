<?php

namespace App\Events;

use App\Enums\BroadcastType;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class FriendRequestEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $receiver;
    public $broadcastType;

    /**
     * Create a new event instance.
     */
    public function __construct(User $sender, User $receiver, BroadcastType $broadcastType)
    {
        $user = new User();
        $dummyUser = $user->forceFill($sender->toArray())->decryptUser();
        $dummyUser->avatar = $sender->getAttributes()['avatar'];

        $user2 = new User();
        $dummyUser2 = $user2->forceFill($receiver->toArray())->decryptUser();
        $dummyUser2->avatar = $sender->getAttributes()['avatar'];

        $this->sender = $dummyUser;
        $this->receiver = $dummyUser2;
        $this->broadcastType = $broadcastType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('friend-request.' . $this->receiver->id),
        ];
    }
}
