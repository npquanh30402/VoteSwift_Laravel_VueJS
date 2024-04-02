<?php

namespace App\Http\Controllers\Api;

use App\Events\VotingProcess;
use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function startVote(VotingRoom $room)
    {
        if (Auth::user()->id !== $room->user_id) {
            abort(403, 'You are not authorized to start the vote.');
        }

        $room->startVote();

        broadcast(new VotingProcess($room));

        return response()->json(['message' => 'Vote started successfully']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VotingRoom $votingRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VotingRoom $votingRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VotingRoom $votingRoom)
    {
        //
    }
}
