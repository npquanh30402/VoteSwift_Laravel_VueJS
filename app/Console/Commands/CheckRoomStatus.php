<?php

namespace App\Console\Commands;

use App\Models\VotingRoom;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'room:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of rooms and end them if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rooms = VotingRoom::where('has_ended', 0)->get();
        foreach ($rooms as $room) {
            $now = Carbon::now();
            if ($now->gt(Carbon::parse($room->end_time))) {
                $room->has_ended = 1;
                $room->endVote();
                $room->save();
//                event(new RoomEnded($room));
            }
        }
    }
}
