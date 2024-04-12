<?php

namespace App\Events;

use App\Enums\BroadcastType;
use App\Models\Candidate;
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
    public $question;
    public $question_type;
    public $question_id;
    public $candidate;
    public $candidate_id;

    // Voting Chat
    public $message;
    public $plain_message;

    // Voting Time
    public $join_time;
    public $leave_time;

    public $created_at;

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
            $this->question = Question::find($question_id)->decryptQuestion()->only(['id', 'question_title', 'question_description']);
            $this->question_id = $question_id;
            $this->question_type = Question::find($question_id)->only(['allow_multiple_votes']);
        }

        if ($candidate_id !== null) {
            $this->candidate = Candidate::find($candidate_id)->decryptCandidate()->only(['id', 'candidate_title', 'candidate_description']);
            $this->candidate_id = $candidate_id;
        }

        if ($message !== null) {
            $this->message = $message;
            $this->plain_message = Crypt::decryptString($message->content);
        }

        if ($broadcast_type === BroadcastType::VOTING_JOIN) {
            $this->join_time = now();
        }

        if ($broadcast_type === BroadcastType::VOTING_LEAVE) {
            $this->leave_time = now();
        }

        $this->created_at = now();

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
