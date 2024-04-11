<?php

namespace App\Events;

use App\Enums\BroadcastType;
use App\Models\Question;
use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class VotingProcess implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // General
    public $user;
    public $room;

    // Voting Choices
    public $question_type;
    public $question_id;
    public $candidate_id;

    // Voting Chat
    public $message;
    public $plain_message;

    public $broadcast_type;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user = null, VotingRoom $room = null, $question_id = null, $candidate_id = null, $message = null, BroadcastType $broadcast_type = null)
    {
        if ($user !== null) {
            $this->user = $user->only(['id', 'username', 'avatar']);
        }

        if ($room !== null) {
            $this->room = $room;
        }

        if ($question_id !== null) {
            $this->question_id = $question_id;
            $this->question_type = Question::find($question_id)->only(['allow_multiple_votes']);
        }

        if ($candidate_id !== null) {
            $this->candidate_id = $candidate_id;
        }

        if ($message !== null) {
            $this->message = $message;
            $this->plain_message = Crypt::decryptString($message->content);
        }

        $this->broadcast_type = $broadcast_type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('voting.process.' . $this->room->id);

        $presenceChannel = new PresenceChannel('voting.process.' . $this->room->id);

        return [$privateChannel, $presenceChannel];
    }
}
