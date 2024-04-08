<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotingRoomRequest;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Notifications\RoomCreation;
use App\Notifications\RoomPublish;
use App\Services\VotingRoomService;
use Carbon\Carbon;
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

        $room->user->notify(new RoomPublish($room));

        InvitationController::sendInvitation($room);

        return back()->with('success', 'Voting room published successfully!');
    }

    public function showPublicRoom()
    {
        $public_rooms = VotingRoom::getPublicRooms()->paginate(9);

        $public_rooms->each(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
            $room->room_description = Crypt::decryptString($room->room_description);

            return $room;
        });

        return Inertia::render('Voting/PublicRooms', compact('public_rooms'));
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
    }

    public function dashboard(VotingRoom $room)
    {
        $this->authorize('view', $room);

        $room->decryptVotingRoom();

        $nestedResults = Vote::getQuestionResults($room->questions);
        $voteCountsInTimeInterval = Vote::calculateVoteCountsInTimeInterval($room);

        return Inertia::render('Voting/VotingRoom/Dashboard', compact('room', 'nestedResults', 'voteCountsInTimeInterval'));
    }

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

            return redirect()->route('dashboard.user');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
