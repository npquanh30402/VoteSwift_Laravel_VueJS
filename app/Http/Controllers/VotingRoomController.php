<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotingRoomRequest;
use App\Models\User;
use App\Models\VotingRoom;
use App\Models\VotingRoomSetting;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VotingRoomController extends Controller
{
    public function showPublicRoom()
    {
        $orgPublicRooms = DB::table('voting_rooms')
            ->join('voting_room_settings', 'voting_room_settings.voting_room_id', '=', 'voting_rooms.id')
            ->where('public_visibility', '=', 1)->get();

        $public_rooms = $orgPublicRooms->transform(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
            $room->room_description = Crypt::decryptString($room->room_description);

            return $room;
        });

        return Inertia::render('Voting/PublicRooms', [
            'publicRooms' => $public_rooms,
        ]);
    }

    public function delete(VotingRoom $room)
    {
        $room->delete();

        return back()->with('success', 'Voting room deleted successfully!');
    }

    public function showUpdateRoomForm(VotingRoom $room)
    {
        $timezones_with_offset = $this->getTimezonesWithOffset();

        $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
        $room->room_description = Crypt::decryptString(strip_tags($room->room_description));
        $room_settings = $room->settings;

        return Inertia::render('Voting/UpdateRoom', compact('room', 'room_settings', 'timezones_with_offset'));
    }

    public function update(VotingRoomRequest $request, VotingRoom $room)
    {
//        dd($request->all(), $room);
        $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
        $room->room_description = Crypt::encryptString(strip_tags($request->room_description));
        $room->timezone = $request->timezone;

        $startTime = Carbon::parse($request->start_time)->setTimezone($request->timezone);
        $endTime = Carbon::parse($request->end_time)->setTimezone($request->timezone);

        $room->start_time = $startTime;
        $room->end_time = $endTime;
        $room->timezone = $request->timezone;

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

        if ($request->disable_password) {
            $settings->password = null;
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

        return Inertia::render('Voting/RoomDetails', compact('room', 'settings', 'timezones_with_offset'));
    }

    private function getTimezonesWithOffset()
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

        return $timezones_with_offset;
    }

    public function create()
    {
        $timezones_with_offset = $this->getTimezonesWithOffset();
        $user_list = User::all()->pluck('username', 'id');

        return Inertia::render('Voting/CreateRoom', [
            'timezones_with_offset' => $timezones_with_offset,
            'user_list' => $user_list
        ]);
    }

    public function store(VotingRoomRequest $request)
    {
//        dd($request->all());
        try {
            if (auth()->user()->rooms()->where('room_name', $request->room_name)->exists()) {
                return back()->with('error', 'Voting room already exists!');
            }

            $room = new VotingRoom();

            $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
            $room->room_description = Crypt::encryptString(strip_tags($request->room_description));

            $room->user_id = auth()->user()->id;

            $startTime = Carbon::parse($request->start_time)->setTimezone($request->timezone);
            $endTime = Carbon::parse($request->end_time)->setTimezone($request->timezone);

            $room->start_time = $startTime;
            $room->end_time = $endTime;
            $room->timezone = $request->timezone;

            $room->save();

            if (!empty($request->files)) {
                $fileBag = $request->files;
                $files = $fileBag->get('files', []);
                foreach ($files as $file) {
                    $filePath = $room->id . '-' . uniqid('', true) . '.' . $file->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('uploads/rooms', $file, $filePath);

                    $oriFileName = $file->getClientOriginalName();

                    $room->files()->create([
                        'voting_room_id' => $room->id,
                        'file_name' => $oriFileName,
                        'file_path' => $filePath,
                    ]);
                }
            }

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

            return redirect()->route('dashboard.user')->with('success', 'Voting room created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
