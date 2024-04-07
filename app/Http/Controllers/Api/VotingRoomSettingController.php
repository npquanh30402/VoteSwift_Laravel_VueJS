<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use Illuminate\Http\Request;

class VotingRoomSettingController extends Controller
{
    public function getSettings(VotingRoom $room)
    {
        $settings = $room->settings;

        return response()->json($settings);
    }

    public function updateInvitationSetting(VotingRoom $room, Request $request)
    {
        if (isset($request->invitation_only)) {
            $room->settings()->update(['invitation_only' => $request->invitation_only === 'true']);
        }

        if (isset($request->wait_for_voters)) {
            $room->settings()->update(['wait_for_voters' => $request->wait_for_voters === 'true']);
        }
    }
}
