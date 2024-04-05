<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $messageObj;
    public $plainMessage;
    public $filePath;

    public function __construct(User $user, $message, $plainMessage, $filePath = null)
    {
        $this->user = $user;
        $message->encrypted_content = Crypt::decryptString($message->encrypted_content);
        $this->messageObj = $message;
        $this->filePath = $filePath;

        $this->plainMessage = $plainMessage;
    }

    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('chat.' . $this->user->id);

        return [$privateChannel];
    }
}
