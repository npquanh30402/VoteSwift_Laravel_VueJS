<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ChatController extends Controller
{
    public function index(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        $decryptedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'avatar' => User::find($message->sender_id)->avatar,
                'sender' => User::find($message->sender_id)->username,
                'message' => Crypt::decryptString($message->encrypted_content),
                'file' => $message->file,
                'send_date' => $message->created_at
            ];
        });

        return response()->json(compact('decryptedMessages'));
    }

    public function store(Request $request, User $user)
    {
        $message = new Message();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $request->user()->id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();

            $fileName = HelperService::sanitizeFileName($fileName);

            $file->storeAs('uploads/messages', $fileName, 'public');

            $message->file = $fileName;
            $message->encrypted_content = HelperService::encryptAndStripTags(null);
        } else {
            $message->encrypted_content = HelperService::encryptAndStripTags($request->message ? $request->message : null);
        }

        $message->sender_id = auth()->user()->id;
        $message->receiver_id = $user->id;

        $message->save();

        broadcast(new MessageSent($request->user(), $message, $request->message));

        return response()->json('Message has been broadcast', 201);
    }
}
