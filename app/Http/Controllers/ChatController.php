<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function main(User $user)
    {
        if ($user->id == auth()->id()) {
            return back()->with('error', 'You cannot chat with yourself.');
        }

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
//                'message' => $message->encrypted_content,
                'file' => $message->file,
                'send_date' => $message->created_at
            ];
        });

        $acceptedFriendsFrom = auth()->user()->acceptedFriendsFrom()->get();
        $acceptedFriendsTo = auth()->user()->acceptedFriendsTo()->get();

        $friends = $acceptedFriendsFrom->merge($acceptedFriendsTo);

        return Inertia::render('Users/UserChat', compact('user', 'friends', 'decryptedMessages'));
    }

//    public function messageReceived(Request $request, User $user)
//    {
//        $message = new Message();
//        if ($request->message != null) {
//            $encryptedContent = Crypt::encryptString($request->message);
//
//            $message->sender_id = auth()->user()->id;
//            $message->receiver_id = $user->id;
//            $message->encrypted_content = $encryptedContent;
//            $message->save();
//        }
//
//        $imagePath = null;
////        if ($request->hasFile('image')) {
////            $image = $request->file('image');
////            $imagePath = 'data:image/' . $image->getClientOriginalExtension() . ';base64,' . base64_encode(file_get_contents($image));
////        }
//
//        if ($request->hasFile('image')) {
//            $image = $request->file('image');
//            $fileName = uniqid('', true) . '.' . $image->getClientOriginalExtension();
//            $image->storeAs('images/messages', $fileName, 'public');
//            $message->image = $fileName;
//            $message->save();
//            $imagePath = asset('storage/images/messages/' . $fileName);
//        }
//
//        broadcast(new MessageSent($request->user(), $request->message, $imagePath));
//
//        return response()->json('Message has been broadcast', 200);
//    }

    public function messageReceived(Request $request, User $user)
    {
        $message = new Message();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $request->user()->id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/messages', $fileName, 'public');
            $message->file = $fileName;
            $message->encrypted_content = Crypt::encryptString(null);
        } else {
            $message->encrypted_content = Crypt::encryptString($request->message ? $request->message : null);
        }

        $message->sender_id = auth()->user()->id;
        $message->receiver_id = $user->id;

        $message->save();

//        $filePath = $request->hasFile('file') ? asset('storage/uploads/messages/' . $fileName) : null;

        broadcast(new MessageSent($request->user(), $message, $request->message));

        return response()->json('Message has been broadcast', 200);
    }
}
