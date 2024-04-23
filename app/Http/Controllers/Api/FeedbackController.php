<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\User;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FeedbackController extends Controller
{
    public function store(VotingRoom $room, User $user, Request $request)
    {
        DB::beginTransaction();
        try {
            $feedback = new Feedback();

            if (!isset($request->feedback)) {
                throw new RuntimeException('Feedback is required');
            }

            $feedback->feedback = HelperService::encryptAndStripTags($request->feedback);

            $feedback->user_id = $user->id;
            $feedback->voting_room_id = $room->id;

            $feedback->save();

            DB::commit();

            return response()->json([
                'data' => $feedback->decryptFeedback(),
                'message' => 'Feedback created successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
