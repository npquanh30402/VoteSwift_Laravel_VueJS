<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class VotingRoomSettingController extends Controller
{
    public function getSettings(VotingRoom $room)
    {
        $settings = $room->settings;

        return response()->json($settings);
    }

    public function updateSettings(VotingRoom $room, Request $request)
    {
        try {
            $updates = [];

            if (isset($request->invitation_only)) {
                $updates['invitation_only'] = $request->invitation_only === 'true';
            }

            if (isset($request->wait_for_voters)) {
                $updates['wait_for_voters'] = $request->wait_for_voters === 'true';
            }

            if (isset($request->public_visibility)) {
                $updates['public_visibility'] = $request->public_visibility === 'true';
            }

            if (isset($request->require_password) && HelperService::convertNullStringToNull($request->require_password)) {
                $updates['password'] = Crypt::encryptString($request->require_password);
            } else {
                $updates['password'] = null;
            }

            if (isset($request->chat_enabled)) {
                $updates['chat_enabled'] = $request->chat_enabled === 'true';
            }

            if (isset($request->chat_messages_saved)) {
                $updates['chat_messages_saved'] = $request->chat_messages_saved === 'true';
            }

            if (isset($request->allow_voters_upload)) {
                $updates['allow_voters_upload'] = $request->allow_voters_upload === 'true';
            }

            $room->settings()->update($updates);

            return response()->json($room->settings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
