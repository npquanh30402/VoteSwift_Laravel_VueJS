<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResultUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $nestedResults;
    public $voteCountsInTimeInterval;

    public function __construct($nestedResults, $voteCountsInTimeInterval)
    {
        $this->nestedResults = $nestedResults;
        $this->voteCountsInTimeInterval = $voteCountsInTimeInterval;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('result-update'),
        ];
    }
}
