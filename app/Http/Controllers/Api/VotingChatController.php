<?php

namespace App\Http\Controllers\Api;

use App\Events\VotingChat;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\VotingMessage;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class VotingChatController extends Controller
{
    public function index(VotingRoom $room)
    {
        $query_messages = VotingMessage::where(function ($query) use ($room) {
            $query->where('room_id', $room->id);
        })->orderBy('created_at', 'asc')->get();

//        $messages = $query_messages->map(function ($message) {
//            return [
//                'id' => $message->id,
//                'sender_id' => $message->sender_id,
//                'sender_username' => User::find($message->sender_id)->username,
//                'sender_avatar' => User::find($message->sender_id)->avatar,
//                'content' => Crypt::decryptString($message->content),
//                'file' => $message->file,
//                'send_date' => $message->created_at
//            ];
//        });

        $messages = $query_messages->map(function ($message) {
            return [
                'id' => $message->id,
                'user' => User::find($message->sender_id),
                'message' => $message,
                'plainMessage' => Crypt::decryptString($message->content)
            ];
        });

        return response()->json(compact('messages'));
    }

    public function store(Request $request, VotingRoom $room)
    {
        $message = new VotingMessage();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $request->user()->id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();

            $fileName = HelperService::sanitizeFileName($fileName);

            $file->storeAs('uploads/messages', $fileName, 'public');

            $message->file = $fileName;
            $message->content = HelperService::encryptAndStripTags(null);
        } else {
            $message->content = HelperService::encryptAndStripTags($request->message ? $request->message : null);
        }

        $message->sender_id = auth()->user()->id;
        $message->room_id = $room->id;

        $message->save();

        broadcast(new VotingChat(Auth::user(), $room, $message));

        return response()->json('Message has been broadcast', 201);
    }
}
