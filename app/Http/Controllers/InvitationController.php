<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
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




//    public function store(VotingRoom $room, Request $request)
//    {
//        foreach ($request->user_ids as $invitationData) {
//            if ($invitationData['id'] == auth()->user()->id) {
//                continue;
//            }
//
//            if (Invitation::where('voting_room_id', $room->id)
//                ->where('invited_user_id', $invitationData['id'])
//                ->exists()) {
//                continue;
//            }
//
//            Invitation::create([
//                'voting_room_id' => $room->id,
//                'invited_user_id' => $invitationData['id'] ?? null,
//            ]);
//        }
//
//        return back()->with('success', 'Invitations added successfully');
//    }

    public function delete(Invitation $invitation)
    {
        $invitation->delete();

        return back()->with('success', 'Invitation deleted successfully');
    }
}
