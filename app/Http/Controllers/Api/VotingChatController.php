<?php

namespace App\Http\Controllers\Api;

use App\Enums\BroadcastType;
use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VotingMessage;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class VotingChatController extends Controller
{
    public function index(VotingRoom $room)
    {
        try {
            $query_messages = VotingMessage::where(function ($query) use ($room) {
                $query->where('room_id', $room->id);
            })->orderBy('created_at', 'asc')->get();

            $messages = $query_messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user' => User::find($message->sender_id),
                    'message' => $message,
                    'plainMessage' => Crypt::decryptString($message->content)
                ];
            });

            return response()->json([
                'data' => $messages,
                'message' => 'Messages retrieved successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $message = new VotingMessage();

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $request->user()->id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();

                $fileName = HelperService::sanitizeFileName($fileName);

                $file->storeAs('uploads/messages', $fileName, 'public');

                $message->file = $fileName;
                $message->content = HelperService::encryptAndStripTags(null);
            } else {
                $message->content = HelperService::encryptAndStripTags($request->message ?: null);
            }

            $message->sender_id = auth()->user()->id;
            $message->room_id = $room->id;

            if ($room->settings->chat_messages_saved) {
                $message->save();
            } else {
                $message->created_at = now();
            }

            DB::commit();

            broadcast(new VotingProcess(user: $user, room: $room, message: $message, broadcast_type: BroadcastType::VOTING_CHAT));

            return response()->json([
//                'data' => $message,
                'message' => 'Message sent successfully',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
