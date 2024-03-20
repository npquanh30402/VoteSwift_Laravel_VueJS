<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        return Inertia::render('Index/Index', [
            'user' => auth()->user(),
        ]);
    }

    public function showPublicRoom()
    {
        $paginator = DB::table('voting_rooms')
            ->join('voting_room_settings', 'voting_room_settings.voting_room_id', '=', 'voting_rooms.id')
            ->where('public_visibility', '=', 1)
            ->paginate(9);

        $public_rooms = $paginator->getCollection()->transform(function ($room) {
            $room->room_name = Crypt::decryptString($room->room_name);
            $room->room_description = Crypt::decryptString($room->room_description);

            return $room;
        });

//        $public_rooms = (new VotingRoom())->getPublicRooms()->transform(function ($room) {
//            $room->room_name = Crypt::decryptString($room->room_name);
//            $room->room_description = Crypt::decryptString($room->room_description);
//
//            return $room;
//        });

        return view('public-room', compact('public_rooms', 'paginator'));
    }
}
