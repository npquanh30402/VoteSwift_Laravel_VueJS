<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VotingRoomRequest;
use App\Models\VotingRoom;
use App\Services\NotificationService;
use App\Services\VotingRoomService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VotingRoomController extends Controller
{
    protected VotingRoomService $votingRoomService;
    protected NotificationService $notificationService;

    public function __construct(VotingRoomService $votingRoomService, NotificationService $notificationService)
    {
        $this->votingRoomService = $votingRoomService;
        $this->notificationService = $notificationService;
    }

    public function index(): JsonResponse
    {
        try {
            $rooms = $this->votingRoomService->getUserRooms();

            return response()->json($rooms);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error getting voting rooms: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, VotingRoom $room): JsonResponse
    {
        try {
            $room = $this->votingRoomService->updateVotingRoom($request, $room);

            return response()->json($room->decryptVotingRoom());
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating voting room: ' . $e->getMessage()], 500);
        }
    }

    public function delete(VotingRoom $room): ?JsonResponse
    {
        try {
            $this->votingRoomService->deleteVotingRoom($room);

            return response()->json(['message' => 'Voting room deleted successfully!'], 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting voting room: ' . $e->getMessage()], 500);
        }
    }

    public function store(VotingRoomRequest $request): ?JsonResponse
    {
        try {
            $room = $this->votingRoomService->storeVotingRoom($request);
            $this->notificationService->sendRoomCreationNotification($room);

            return response()->json($room->decryptVotingRoom(), 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating voting room: ' . $e->getMessage()], 500);
        }
    }
}
