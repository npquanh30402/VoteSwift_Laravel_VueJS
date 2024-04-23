<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VotingRoomRequest;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Notifications\RoomPublish;
use App\Services\HelperService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class VotingRoomController extends Controller
{
//    protected VotingRoomService $votingRoomService;
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handleTieAndCreateNewVotingRound(VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            $winningOptions = Vote::getWinningOptions($room);
            $winningQuestions = [];
            $winningCandidates = [];

            $keysWithMultipleCandidates = array_filter(array_keys($winningOptions), function ($key) use ($winningOptions) {
                return count($winningOptions[$key]) >= 2;
            });

            foreach ($keysWithMultipleCandidates as $key) {
                $question = Question::findOrFail($key);
                $winningQuestions[] = $question;

                foreach ($winningOptions[$key] as $candidateId) {
                    $candidate = Candidate::findOrFail($candidateId);
                    $winningCandidates[] = $candidate;
                }
            }

            $original = $room->decryptVotingRoom();
            $new_room_name = $this->incrementRound($original->room_name);

            $copy = $original->replicate()->fill([
                'room_name' => HelperService::encryptAndStripTags($new_room_name),
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

            foreach ($winningQuestions as $question) {
                $oldQuestion = $question->decryptQuestion();

                $newQuestion = $oldQuestion->replicate()->fill([
                    'question_title' => HelperService::encryptAndStripTags($oldQuestion->question_title),
                    'question_description' => HelperService::encryptAndStripTags($oldQuestion->question_description),
                    'question_image' => $oldQuestion->question_image
                ]);
                $newQuestion->voting_room_id = $copy->id;
                $newQuestion->save();

                foreach ($winningCandidates as $candidate) {
                    if ($candidate->question_id !== $question->id) {
                        continue;
                    }

                    $oldCandidate = $candidate->decryptCandidate();

                    $newCandidate = $oldCandidate->replicate()->fill([
                        'candidate_title' => HelperService::encryptAndStripTags($oldCandidate->candidate_title),
                        'candidate_description' => HelperService::encryptAndStripTags($oldCandidate->candidate_description),
                        'candidate_image' => $oldCandidate->candidate_image,
                    ]);

                    $newCandidate->question_id = $newQuestion->id;
                    $newCandidate->save();
                }
            }

            foreach ($original->attachments as $attachment) {
                $newAttachment = $attachment->replicate();
                $newAttachment->voting_room_id = $copy->id;

                $newAttachment->save();
            }

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

    function incrementRound($room_name)
    {
        if (preg_match('/\[Round (\d+)\]/', $room_name, $matches)) {
            $current_round = (int)$matches[1];
            return str_replace("[Round $current_round]", "[Round " . ($current_round + 1) . "]", $room_name);
        }

        return "[Round 2] $room_name";
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

    public function show(VotingRoom $room): JsonResponse
    {
        try {
            return response()->json([
                'data' => $room->decryptVotingRoom(),
                'message' => 'Voting room retrieved successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                throw new RuntimeException('User is null');
            }

            $rooms = $user->rooms()->get()->transform(function ($room) {
                if (Carbon::now()->gt(Carbon::parse($room->end_time)))
                    $room->endVote();

                return $room->decryptVotingRoom();
            });

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
        DB::beginTransaction();
        try {
            if (isset($request->room_name)) {
                $room->room_name = HelperService::encryptAndStripTags($request->room_name);
            }

            if (isset($request->room_description)) {
                $room->room_description = HelperService::encryptAndStripTags($request->room_description);
            }

            if (isset($request->activeTz)) {
                $room->timezone = $request->activeTz;
            }

            if (isset($request->start_time)) {
                $room->start_time = Carbon::parse($request->start_time)->setTimezone('UTC');
            }

            if (isset($request->end_time)) {
                $room->end_time = Carbon::parse($request->end_time)->setTimezone('UTC');
            }

            if (isset($request->end_time)) {
                $room->end_time = Carbon::parse($request->end_time);
            }

//            if (isset($request->is_published)) {
//                $this->publish($room);
//            }

            $room->save();

            DB::commit();

            return response()->json([
                'data' => $room->decryptVotingRoom(),
                'message' => 'Voting room updated successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function publish(VotingRoom $room)
    {
        DB::beginTransaction();
        try {
            if ($room->is_published) {
                throw new RuntimeException('Voting room is already published!');
            }

            if ($room->questions()->count() < 1) {
                throw new RuntimeException('Voting room must have at least 1 question!');
            }

            if ($room->start_time == null || $room->end_time == null) {
                throw new RuntimeException('Voting room must have start and end date!');
            }

            $room->is_published = 1;

            $room->save();

            DB::commit();

            $room->user->notify(new RoomPublish($room));

            if ($room->settings->invitation_only === 1) {
                InvitationController::sendInvitation($room);
            }

            return response()->json([
                'data' => $room->decryptVotingRoom(),
                'message' => 'Voting room published successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(VotingRoom $room): ?JsonResponse
    {
        DB::beginTransaction();
        try {
            $room->delete();

            DB::commit();

            return response()->json(['message' => 'Voting room deleted successfully!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(VotingRoomRequest $request): ?JsonResponse
    {
        DB::beginTransaction();
        try {
            $room = new VotingRoom([
                'room_name' => HelperService::encryptAndStripTags($request->room_name),
                'room_description' => HelperService::encryptAndStripTags($request->room_description),
                'user_id' => Auth::user()->id,
            ]);

            $room->save();

            $room->settings()->create();

            DB::commit();

            $this->notificationService->sendRoomCreationNotification($room);

            return response()->json([
                'data' => VotingRoom::findOrFail($room->id)->decryptVotingRoom(),
                'message' => 'Voting room created successfully!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
