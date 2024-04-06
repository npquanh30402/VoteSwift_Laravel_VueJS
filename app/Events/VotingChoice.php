<?php

namespace App\Events;

use App\Models\Question;
use App\Models\VotingRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VotingChoice implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $question_type;
    public $question_id;
    public $candidate_id;

    /**
     * Create a new event instance.
     */
    public function __construct(VotingRoom $room, $question_id, $candidate_id)
    {
        $this->room = $room;
        $this->question_type = Question::find($question_id)->only(['allow_multiple_votes']);
        $this->question_id = $question_id;
        $this->candidate_id = $candidate_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $privateChannel = new PrivateChannel('voting.choice.' . $this->room->id);

        return [$privateChannel];
    }
}
