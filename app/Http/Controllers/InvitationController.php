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
}
