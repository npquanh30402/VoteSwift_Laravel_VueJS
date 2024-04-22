<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VotingRoomRequest;
use App\Models\VotingRoom;
use App\Services\HelperService;
use App\Services\NotificationService;
use App\Services\VotingRoomService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotingRoomController extends Controller
{
    protected VotingRoomService $votingRoomService;
    protected NotificationService $notificationService;

    public function __construct(VotingRoomService $votingRoomService, NotificationService $notificationService)
    {
        $this->votingRoomService = $votingRoomService;
        $this->notificationService = $notificationService;
    }

    public function duplicate(VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $original = $room->decryptVotingRoom();
            $copy = $original->replicate()->fill([
                'room_name' => HelperService::encryptAndStripTags('[Copied] ' . $original->room_name),
                'room_description' => HelperService::encryptAndStripTags($original->room_description),
                'start_time' => null,
                'end_time' => null,
                'is_published' => 0,
                'vote_started' => 0,
                'has_ended' => 0
            ]);

            $copy->save();

            if ($original->settings) {
                $newSettings = $original->settings->replicate()->fill([
                    'password' => null,
                    'password_qrcode' => null,
                ]);
                $newSettings->voting_room_id = $copy->id;
                $newSettings->save();
            }

            if ($original->invitationMail) {
                $oldMail = $original->invitationMail->decryptInvitationMail();
                $newInvitationMail = $oldMail->replicate()->fill([
                    'mail_subject' => HelperService::encryptAndStripTags($oldMail->mail_subject),
                    'mail_content' => HelperService::encryptAndStripTags($oldMail->mail_content)
                ]);
                $newInvitationMail->voting_room_id = $copy->id;
                $newInvitationMail->save();
            }

            foreach ($original->questions as $question) {
                $oldQuestion = $question->decryptQuestion();

                $newQuestion = $oldQuestion->replicate()->fill([
                    'question_title' => HelperService::encryptAndStripTags($oldQuestion->question_title),
                    'question_description' => HelperService::encryptAndStripTags($oldQuestion->question_description),
                    'question_image' => null
                ]);
                $newQuestion->voting_room_id = $copy->id;
                $newQuestion->save();

                foreach ($question->candidates as $candidate) {
                    if ($candidate->question_id !== $question->id) {
                        continue;
                    }
                    
                    $oldCandidate = $candidate->decryptCandidate();

                    $newCandidate = $oldCandidate->replicate()->fill([
                        'candidate_title' => HelperService::encryptAndStripTags($oldCandidate->candidate_title),
                        'candidate_description' => HelperService::encryptAndStripTags($oldCandidate->candidate_description),
                        'candidate_image' => null,
                    ]);
                    $newCandidate->question_id = $newQuestion->id;
                    $newCandidate->save();
                }
            }

//            foreach ($original->attachments as $attachment) {
//                $newAttachment = $attachment->replicate();
//                $newAttachment->voting_room_id = $copy->id;
//
//                $originalPath = public_path() . $attachment->file_path;
//                $newPath = public_path() . '/storage/uploads/rooms/' . "[$copy->id]" . basename($attachment->file_path);
//
//                if (File::exists($originalPath)) {
//                    File::copy($originalPath, $newPath);
//                    $newAttachment->file_path = 'app/' . $copy->id . '/' . basename($attachment->file_path);
//                }
//
//                $newAttachment->save();
//            }

            foreach ($original->invitations as $invitation) {
                $newInvitation = $invitation->replicate();
                $newInvitation->voting_room_id = $copy->id;
                $newInvitation->save();
            }

            DB::commit();

            return response()->json([
                'data' => $copy->decryptVotingRoom(),
                'message' => 'Voting room duplicated successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function fetchAVotingRoom(VotingRoom $room): JsonResponse
    {
        try {
            $room = $this->votingRoomService->fetchRoom($room);

            return response()->json([
                'data' => $room,
                'message' => 'Voting room retrieved successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $rooms = $this->votingRoomService->getUserRooms();

            return response()->json([
                'data' => $rooms,
                'message' => 'Voting rooms retrieved successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, VotingRoom $room): JsonResponse
    {
        try {
            $room = $this->votingRoomService->updateVotingRoom($request, $room);

            return response()->json([
                'data' => $room->decryptVotingRoom(),
                'message' => 'Voting room updated successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(VotingRoom $room): ?JsonResponse
    {
        try {
            $this->votingRoomService->deleteVotingRoom($room);

            return response()->json(['message' => 'Voting room deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(VotingRoomRequest $request): ?JsonResponse
    {
        try {
            $room = $this->votingRoomService->storeVotingRoom($request);
            $this->notificationService->sendRoomCreationNotification($room);

            return response()->json([
                'data' => $room->decryptVotingRoom(),
                'message' => 'Voting room created successfully!',
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
