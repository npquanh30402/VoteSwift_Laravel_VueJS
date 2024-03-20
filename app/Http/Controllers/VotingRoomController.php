<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotingRoomRequest;
use App\Models\VotingRoom;
use App\Models\VotingRoomSetting;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class VotingRoomController extends Controller
{
    public function delete(VotingRoom $room)
    {
        $room->delete();

        return back()->with('success', 'Voting room deleted successfully!');
    }

    public function update(VotingRoomRequest $request, VotingRoom $room)
    {
        $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
        $room->room_description = Crypt::encryptString(strip_tags($request->room_description));
        $room->timezone = $request->timezone;

        $startTime = Carbon::parse($request->start_time, $request->timezone);
        $endTime = Carbon::parse($request->end_time, $request->timezone);

        $room->start_time = $startTime;
        $room->end_time = $endTime;

        $currentTime = Carbon::now($request->timezone);

        $room->save();

        $settings = $room->settings;

        $settings->update([
            'allow_multiple_votes' => (bool)($request->allow_multiple_votes ?? false),
            'public_visibility' => (bool)($request->public_visibility ?? false),
            'results_visibility' => $request->results_visibility,
            'allow_voting' => (bool)($request->allow_voting ?? false),
            'allow_skipping' => (bool)($request->allow_skipping ?? false),
            'allow_anonymous_voting' => (bool)($request->allow_anonymous_voting ?? false),
        ]);

        if (!empty($request->require_password)) {
            $settings->password = bcrypt($request->require_password);
            $settings->save();
        }

        return back()->with('success', 'Room updated successfully!');
    }

    public function main(VotingRoom $room)
    {
        $timezones = DateTimeZone::listIdentifiers();
        $timezones_with_offset = [];

        foreach ($timezones as $timezone) {
            $datetime = new DateTime('now', new DateTimeZone($timezone));
            $offset = $datetime->getOffset() / 3600;
            $offset_formatted = ($offset >= 0 ? '+' : '') . $offset;
            $timezones_with_offset[$timezone] = $offset_formatted;
        }

        asort($timezones_with_offset);

        $settings = $room->settings;

        $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
        $room->room_description = Crypt::decryptString(strip_tags($room->room_description));

        return view('dashboard.voting', compact('room', 'settings', 'timezones_with_offset'));
    }

    public function store(VotingRoomRequest $request)
    {
        try {
            if (auth()->user()->rooms()->where('room_name', $request->room_name)->exists()) {
                return back()->with('error', 'Voting room already exists!');
            }

            $room = new VotingRoom();

            $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
            $room->room_description = Crypt::encryptString(strip_tags($request->room_description));

            $room->user_id = auth()->user()->id;

            $startTime = Carbon::parse($request->start_time, $request->timezone);
            $endTime = Carbon::parse($request->end_time, $request->timezone);

            $room->start_time = $startTime;
            $room->end_time = $endTime;
            $room->save();

            $passwordRoom = null;
            if (!empty($request->require_password)) {
                $passwordRoom = Hash::make($request->require_password);
            }

            VotingRoomSetting::create([
                'voting_room_id' => $room->id,
                'allow_multiple_votes' => $request->boolean('allow_multiple_votes'),
                'public_visibility' => $request->boolean('public_visibility'),
                'password' => $passwordRoom,
                'results_visibility' => $request->results_visibility,
                'allow_voting' => $request->boolean('allow_voting'),
                'allow_skipping' => $request->boolean('allow_skipping'),
                'allow_anonymous_voting' => $request->boolean('allow_anonymous_voting'),
            ]);

            return back()->with('success', 'Voting room created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function getVotingRoomForm()
    {
        return view('voting.create-room');
    }
}
