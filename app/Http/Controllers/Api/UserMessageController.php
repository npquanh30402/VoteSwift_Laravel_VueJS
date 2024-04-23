<?php

namespace App\Http\Controllers\Api;

use App\Events\UserMessageEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMessage;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserMessageController extends Controller
{
    public function markAsRead(User $sender, User $receiver)
    {
        DB::beginTransaction();
        try {
            UserMessage::where('receiver_id', $receiver->id)
                ->where('sender_id', $sender->id)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Messages marked as read successfully',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function fetchUnreadMessages(User $user)
    {
        try {
            $friendsIds = $user->acceptedFriendsFrom()->pluck('users.id')->merge($user->acceptedFriendsTo()->pluck('users.id'));

            $messages = UserMessage::whereIn('sender_id', $friendsIds)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->get();

            $messages = $messages->map(function ($message) {
                return [
                    'message' => $message->decryptUserMessage(),
                    'user' => User::find($message->sender_id)->only('id', 'username', 'avatar'),
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

    public function index(User $sender, User $receiver)
    {
        try {
            $query_messages = UserMessage::where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender->id)
                    ->where('receiver_id', $receiver->id);
            })->orWhere(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $receiver->id)
                    ->where('receiver_id', $sender->id);
            })->orderBy('created_at', 'asc')->get();

            $messages = $query_messages->map(function ($message) {
                return [
                    'message' => $message->decryptUserMessage(),
                    'user' => User::find($message->sender_id)->only('id', 'username', 'avatar'),
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

    public function store(User $sender, User $receiver, Request $request)
    {
        DB::beginTransaction();
        try {
            $message = new UserMessage();

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

            $message->sender_id = $sender->id;
            $message->receiver_id = $receiver->id;

            $message->save();

            $message = $message->decryptUserMessage();

            $data = [
                'message' => $message,
                'user' => $sender->only('id', 'username', 'avatar'),
            ];

            DB::commit();

            broadcast(new UserMessageEvent(user: $sender, message: $message))->toOthers();

            return response()->json([
                'data' => $data,
                'message' => 'Message has been broadcast',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
