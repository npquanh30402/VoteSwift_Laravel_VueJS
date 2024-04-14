<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvitationMailRequests\StoreAndUpdateInvitationMailRequest;
use App\Models\InvitationMail;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Exception;
use Illuminate\Http\JsonResponse;

class InvitationMailController extends Controller
{
    public function index(VotingRoom $room)
    {
        try {
            $mail = $room->invitationMail->decryptInvitationMail();

            return response()->json([
                'data' => $mail,
                'message' => 'Invitation mail retrieved successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invitation mail not found.',
            ], 404);
        }
    }

    public function storeOrUpdate(VotingRoom $room, StoreAndUpdateInvitationMailRequest $request): JsonResponse
    {
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

        return response()->json([
            'data' => $mail->decryptInvitationMail(),
            'message' => $message,
        ]);
    }

    public function destroy(InvitationMail $invitationMail)
    {
        try {
            $invitationMail->delete();

            return response()->json([
                'message' => 'Invitation mail deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invitation mail not found.',
            ], 404);
        }
    }
}
