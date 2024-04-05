<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function main()
    {
        $acceptedFriendsFrom = auth()->user()->acceptedFriendsFrom()->get();
        $acceptedFriendsTo = auth()->user()->acceptedFriendsTo()->get();

        $friends = $acceptedFriendsFrom->merge($acceptedFriendsTo);

        return Inertia::render('Users/UserChat', compact('friends'));
    }

    public function messageReceived(Request $request, User $user)
    {
        $message = new Message();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $request->user()->id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();

            $fileName = HelperService::sanitizeFileName($fileName);

            $file->storeAs('uploads/messages', $fileName, 'public');

            $message->file = $fileName;
            $message->encrypted_content = Crypt::encryptString(null);
        } else {
            $message->encrypted_content = Crypt::encryptString($request->message ? $request->message : null);
        }

        $message->sender_id = auth()->user()->id;
        $message->receiver_id = $user->id;

        $message->save();

        broadcast(new MessageSent($request->user(), $message, $request->message));

        return response()->json('Message has been broadcast', 200);
    }
}
