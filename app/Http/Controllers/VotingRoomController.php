<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotingRoomRequest;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Notifications\RoomCreation;
use App\Services\VotingRoomService;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;

class VotingRoomController extends Controller
{
    protected $votingRoomService;

    public function __construct(VotingRoomService $votingRoomService)
    {
        $this->votingRoomService = $votingRoomService;
    }

    public function publishRoom(VotingRoom $room)
    {
        if ($room->is_published) {
            return back()->with('error', 'Voting room is already published!');
        }

        if ($room->questions()->count() < 1) {
            return back()->with('error', 'Voting room must have at least 1 question!');
        }

        if ($room->start_time == null || $room->end_time == null) {
            return back()->with('error', 'Voting room must have start and end date!');
        }

        $room->is_published = true;

        $room->save();

        return back()->with('success', 'Voting room published successfully!');
    }

//    public function showDescription(VotingRoom $room)
//    {
//        $room->room_name = Crypt::decryptString($room->room_name);
//        $room->room_description = Crypt::decryptString($room->room_description);
//
//        return Inertia::render('Voting/VotingRoom/DescriptionPage', compact('room'));
//    }
//
//    public function showAttachment(VotingRoom $room)
//    {
//        $attachments = $room->attachments()->get();
//
//        $room->room_name = Crypt::decryptString($room->room_name);
//        $room->room_description = Crypt::decryptString($room->room_description);
//
//        return Inertia::render('Voting/VotingRoom/AttachmentPage', compact('attachments', 'room'));
//    }

    public function showPublicRoom()
    {
        $public_rooms = VotingRoom::getPublicRooms()->paginate(9);

        $public_rooms->each(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
            $room->room_description = Crypt::decryptString($room->room_description);

            return $room;
        });

        return Inertia::render('Voting/PublicRooms', [
            'publicRooms' => $public_rooms,
        ]);
    }

    public function delete(VotingRoom $room): RedirectResponse
    {
        try {
            $this->votingRoomService->deleteVotingRoom($room);
            return redirect()->route('dashboard.user')->with('success', 'Voting room deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting voting room: ' . $e->getMessage());
        }
    }

//    public function showUpdateRoomForm(VotingRoom $room)
//    {
//        $timezones_with_offset = $this->getTimezonesWithOffset();
//
//        $room->room_name = Crypt::decryptString(strip_tags($room->room_name));
//        $room->room_description = Crypt::decryptString(strip_tags($room->room_description));
//        $room_settings = $room->settings;
//
//        return Inertia::render('Voting/VotingRoom/UpdateRoom', compact('room', 'room_settings', 'timezones_with_offset'));
//    }

    public function update(Request $request, VotingRoom $room)
    {
        if (isset($request->room_name)) {
            $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
        }

        if (isset($request->room_description)) {
            $room->room_description = Crypt::encryptString(strip_tags($request->room_description));
        }

        if (isset($request->activeTz)) {
            $room->timezone = $request->activeTz['tz'];
        }

        if (isset($request->date)) {
            $start_date = Carbon::parse($request->date[0])->setTimezone('UTC');
            $end_date = Carbon::parse($request->date[1])->setTimezone('UTC');

            $room->start_time = $start_date;
            $room->end_time = $end_date;
        }

        $room->save();

        $settings = $room->settings;

        if (isset($request->require_password) && $request->require_password !== null) {
            $settings->password = bcrypt($request->require_password);
        } else {
            $settings->password = null;
        }
        $settings->save();

//        $settings->update([
//            'allow_multiple_votes' => (bool)($request->allow_multiple_votes ?? false),
//            'public_visibility' => (bool)($request->public_visibility ?? false),
//            'results_visibility' => $request->results_visibility,
//            'allow_voting' => (bool)($request->allow_voting ?? false),
//            'allow_skipping' => (bool)($request->allow_skipping ?? false),
//            'allow_anonymous_voting' => (bool)($request->allow_anonymous_voting ?? false),
//        ]);

        return back()->with('success', 'Room updated successfully!');
    }

    public function dashboard(VotingRoom $room)
    {
        $room->decryptVotingRoom();

        $room_settings = $room->settings;

        $nestedResults = Vote::getQuestionResults($room->questions);
        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        $room_questions = $room->questions->map(function ($question) {
            $question->question_title = Crypt::decryptString($question->question_title);
            $question->question_description = Crypt::decryptString($question->question_description);
            return $question;
        });

        $room_attachments = $room->attachments;

//        $user_list = User::all()->map(function ($user) {
//            return [
//                'id' => $user->id,
//                'username' => $user->username,
//                'email' => $user->email,
//                'avatar' => $user->avatar
//            ];
//        });

        return Inertia::render('Voting/VotingRoom/Dashboard', compact('room', 'room_settings', 'room_questions', 'room_attachments', 'nestedResults', 'voteCountsInTimeInterval'));
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

        return Inertia::render('Voting/VotingRoom/RoomDetails', compact('room', 'settings', 'timezones_with_offset'));
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

//    public function create()
//    {
//        $timezones_with_offset = $this->getTimezonesWithOffset();
//        $user_list = User::all()->pluck('username', 'id');
//
//        return Inertia::render('Voting/VotingRoom/CreateRoom', [
//            'timezones_with_offset' => $timezones_with_offset,
//            'user_list' => $user_list
//        ]);
//    }

    public function create()
    {
        return Inertia::render('Voting/VotingRoom/CreateRoom');
    }

    public function store(VotingRoomRequest $request)
    {
        try {
            if (auth()->user()->rooms()->where('room_name', $request->room_name)->exists()) {
                return back()->with('error', 'Voting room already exists!');
            }

            $room = new VotingRoom([
                'room_name' => Crypt::encryptString(strip_tags($request->room_name)),
                'room_description' => Crypt::encryptString(strip_tags($request->room_description)),
                'user_id' => auth()->user()->id,
            ]);

            $room->save();

            $room->settings()->create();

            $room->user->notify(new RoomCreation($room));

            return redirect()->route('dashboard.user')->with('success', 'Voting room created successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

//    public function store(VotingRoomRequest $request)
//    {
//        try {
//            if (auth()->user()->rooms()->where('room_name', $request->room_name)->exists()) {
//                return back()->with('error', 'Voting room already exists!');
//            }
//
//            $room = new VotingRoom();
//
//            $room->room_name = Crypt::encryptString(strip_tags($request->room_name));
//            $room->room_description = Crypt::encryptString(strip_tags($request->room_description));
//
//            $room->user_id = auth()->user()->id;
//
//            $startTime = Carbon::parse($request->start_time)->setTimezone($request->timezone);
//            $endTime = Carbon::parse($request->end_time)->setTimezone($request->timezone);
//
//            $room->start_time = $startTime;
//            $room->end_time = $endTime;
//            $room->timezone = $request->timezone;
//
//            $room->save();
//
//            if (!empty($request->files)) {
//                $fileBag = $request->files;
//                $files = $fileBag->get('files', []);
//                foreach ($files as $file) {
//                    $filePath = $room->id . '-' . uniqid('', true) . '.' . $file->getClientOriginalExtension();
//
//                    $filePath = HelperService::sanitizeFileName($filePath);
//
//                    Storage::disk('public')->putFileAs('uploads/rooms', $file, $filePath);
//
//                    $oriFileName = $file->getClientOriginalName();
//
//                    $room->attachments()->create([
//                        'voting_room_id' => $room->id,
//                        'file_name' => $oriFileName,
//                        'file_path' => $filePath,
//                    ]);
//                }
//            }
//
//            $passwordRoom = null;
//            if (!empty($request->require_password)) {
//                $passwordRoom = Hash::make($request->require_password);
//            }
//
//            VotingRoomSetting::create([
//                'voting_room_id' => $room->id,
//                'allow_multiple_votes' => $request->boolean('allow_multiple_votes'),
//                'public_visibility' => $request->boolean('public_visibility'),
//                'password' => $passwordRoom,
//                'results_visibility' => $request->results_visibility,
//                'allow_voting' => $request->boolean('allow_voting'),
//                'allow_skipping' => $request->boolean('allow_skipping'),
//                'allow_anonymous_voting' => $request->boolean('allow_anonymous_voting'),
//            ]);
//
//            return redirect()->route('dashboard.user')->with('success', 'Voting room created successfully!');
//        } catch (Exception $e) {
//            return back()->with('error', $e->getMessage());
//        }
//    }
}
