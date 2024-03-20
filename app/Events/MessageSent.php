<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $messageObj;
    public $message;
    public $filePath;

    public function __construct(User $user, $message, $filePath = null)
    {
        $this->user = $user;
        $this->messageObj = $message;
        $this->message = Crypt::decryptString($message->encrypted_content);
        $this->filePath = $filePath;
    }

    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('chat.' . $this->user->id);

        $presenceChannel = new PresenceChannel('chat');

        return [$privateChannel, $presenceChannel];
    }
}
