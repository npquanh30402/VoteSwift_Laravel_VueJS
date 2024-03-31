<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\VotingRoom;
use App\Notifications\InvitationNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public static function sendInvitation(VotingRoom $room)
    {
        $authUser = Auth::user();
        $invitedUserIds = $room->invitations()->pluck('invited_user_id');

        $startTime = Carbon::parse($room->start_time);
        $endTime = Carbon::parse($room->end_time);
        $durationInMinutes = $endTime->diffInMinutes($startTime);

        foreach ($invitedUserIds as $userId) {
            $user = User::find($userId);

            if ($user) {
                $token = Str::random(64);

                Cache::put("ballot.tkn.{$token}", $user->id, $durationInMinutes);

                $invitationUrl = route('vote.main', ['token' => $token, 'room' => $room]);

                $user->notify(new InvitationNotification($room, $authUser, $user, $invitationUrl));
            }
        }
    }

    public function getInvitations(VotingRoom $room)
    {
        $invitations = $room->invitations;

        $invitedUsers = [];

        foreach ($invitations as $invitation) {
            $invitedUser = User::find($invitation->invited_user_id);
            $invitedUsers[] = $invitedUser;
        }

        return response()->json(compact('invitations', 'invitedUsers'));
    }

    public function store(VotingRoom $room, Request $request)
    {
        $existingInvitations = Invitation::where('voting_room_id', $room->id)
            ->pluck('invited_user_id')
            ->toArray();

        $invitedUserIdsToDelete = array_diff($existingInvitations, array_column($request->user_ids, 'id'));

        Invitation::where('voting_room_id', $room->id)
            ->whereIn('invited_user_id', $invitedUserIdsToDelete)
            ->delete();

        foreach ($request->user_ids as $invitationData) {
            if ($invitationData['id'] == auth()->user()->id) {
                continue;
            }

            if (Invitation::where('voting_room_id', $room->id)
                ->where('invited_user_id', $invitationData['id'])
                ->exists()) {
                continue;
            }

            Invitation::create([
                'voting_room_id' => $room->id,
                'invited_user_id' => $invitationData['id'] ?? null,
            ]);
        }

        return back()->with('success', 'Invitations added successfully');
    }

//    public function delete(Invitation $invitation)
//    {
//        $invitation->delete();
//
//        return back()->with('success', 'Invitation deleted successfully');
//    }
}
