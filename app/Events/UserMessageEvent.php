<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct(User $user, UserMessage $message)
    {
        $this->data = [
            'message' => $message,
            'user' => $user->only('id', 'username', 'avatar'),
        ];
    }

    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('chat.' . $this->data['message']->receiver_id);

        return [$privateChannel];
    }
}
