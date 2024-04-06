<?php

namespace App\Http\Controllers;

use App\Models\VotingRoom;
use Illuminate\Http\Request;

class VotingRoomSettingController extends Controller
{
    public function updateChatSetting(VotingRoom $room, Request $request)
    {
        if (isset($request->chat_enabled)) {
            $room->settings()->update(['chat_enabled' => $request->chat_enabled]);
        }

        if (isset($request->chat_messages_saved)) {
            $room->settings()->update(['chat_messages_saved' => $request->chat_messages_saved]);
        }

        if (isset($request->allow_voters_upload)) {
            $room->settings()->update(['allow_voters_upload' => $request->allow_voters_upload]);
        }
    }

    public function updateInvitationSetting(VotingRoom $room, Request $request)
    {
        $room->settings()->update(['invitation_only' => $request->invitation_only]);
    }

    public function updateWaitForVotersSetting(VotingRoom $room, Request $request)
    {

        $room->settings()->update(['wait_for_voters' => $request->wait_for_voters]);
    }

    public function updatePasswordSetting(VotingRoom $room, Request $request)
    {
        $settings = $room->settings;

        if (isset($request->require_password) && $request->require_password !== null) {
            $settings->password = bcrypt($request->require_password);
        } else {
            $settings->password = null;
        }

        $settings->save();
    }
}
