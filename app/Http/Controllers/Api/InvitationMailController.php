<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationMailRequests\StoreAndUpdateInvitationMailRequest;
use App\Models\InvitationMail;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvitationMailController extends Controller
{
    public function index(VotingRoom $room)
    {
        try {
            $mail = $room->invitationMail;

            if ($mail) {
                return response()->json([
                    'data' => $mail->decryptInvitationMail(),
                    'message' => 'Invitation mail retrieved successfully.',
                ]);
            }

            return response()->json([
                'message' => 'No invitation mail found.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeOrUpdate(VotingRoom $room, StoreAndUpdateInvitationMailRequest $request): JsonResponse
    {
        $this->authorize('update', $room);

        DB::beginTransaction();
        try {
            if ($room->invitationMail) {
                $mail = $room->invitationMail;
                $mail->update([
                    'mail_subject' => HelperService::encryptAndStripTags($request->mail_subject),
                    'mail_content' => HelperService::encryptAndStripTags($request->mail_content)
                ]);
                $message = 'Invitation mail updated successfully.';
            } else {
                $mail = $room->invitationMail()->create([
                    'mail_subject' => HelperService::encryptAndStripTags($request->mail_subject),
                    'mail_content' => HelperService::encryptAndStripTags($request->mail_content)
                ]);
                $message = 'Invitation mail created successfully.';
            }

            DB::commit();

            return response()->json([
                'data' => $mail->decryptInvitationMail(),
                'message' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete(InvitationMail $invitationMail)
    {
        $this->authorize('delete', $invitationMail);
        DB::beginTransaction();
        try {
            $invitationMail->delete();

            DB::commit();

            return response()->json([
                'message' => 'Invitation mail deleted successfully.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
