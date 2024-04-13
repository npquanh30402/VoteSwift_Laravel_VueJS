<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(VotingRoom $room, User $user, Request $request)
    {
        $feedback = new Feedback();

        if (isset($request->feedback)) {
            $feedback->feedback = HelperService::encryptAndStripTags($request->feedback);
        }

        $feedback->user_id = $user->id;
        $feedback->voting_room_id = $room->id;

        $feedback->save();

        return response()->json($feedback);
    }
}
