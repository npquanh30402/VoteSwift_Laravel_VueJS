<?php

namespace App\Http\Controllers;

use App\Models\VotingRoom;
use Illuminate\Http\Request;

class VotingRoomSettingController extends Controller
{
    public function updateInvitationSetting(VotingRoom $room, Request $request)
    {
        $room->settings()->update(['invitation_only' => $request->invitation_only]);

        return back()->with('success', 'Invitation settings updated successfully');
    }
}
