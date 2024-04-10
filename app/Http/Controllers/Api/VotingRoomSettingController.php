<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VotingRoom;
use App\Services\HelperService;
use Carbon\Carbon;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VotingRoomSettingController extends Controller
{
    public function createQRCodeForPassword(VotingRoom $room, string $password)
    {
        $token = Str::random(64);

        $endTime = Carbon::parse($room->end_time);
        $durationUntilEnd = $endTime->diffInSeconds(now());

        Cache::put("room{$room->id}" . "_pwl.tkn.{$token}", $password, $durationUntilEnd);

        $passwordlessUrl = route('vote.main', [
            'token' => $token,
            'room' => $room->id
        ]);

        $writer = new PngWriter();

        $qrCode = QrCode::create($passwordlessUrl)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $label = Label::create('Scan to vote for room ' . $room->id)
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, null, $label);

        $writer->validateResult($result, $passwordlessUrl);

        $fileName = $room->id . '_' . uniqid('', true) . '.png';
        $directory = 'public/images/password/';

        File::makeDirectory(storage_path('app/' . $directory), 0777, true, true);

        $result->saveToFile(storage_path('app/' . $directory . $fileName));

        return $fileName;
    }

    public function getSettings(VotingRoom $room)
    {
        $settings = $room->settings;

        return response()->json($settings);
    }

    public function updateSettings(VotingRoom $room, Request $request)
    {
        try {
            $updates = [];

            if (isset($request->invitation_only)) {
                $updates['invitation_only'] = $request->invitation_only === 'true';
            }

            if (isset($request->wait_for_voters)) {
                $updates['wait_for_voters'] = $request->wait_for_voters === 'true';
            }

            if (isset($request->public_visibility)) {
                $updates['public_visibility'] = $request->public_visibility === 'true';
            }

            if (isset($request->require_password) && HelperService::convertNullStringToNull($request->require_password)) {
                $updates['password'] = Hash::make($request->require_password);
                $fileName = $this->createQRCodeForPassword($room, $request->require_password);

                $updates['password_qrcode'] = $fileName;

                $oldImage = $room->settings->password_qrcode;

                if ($oldImage !== $fileName) {
                    Storage::delete(str_replace('/storage/', 'public/', $oldImage));
                }
            } else {
                $updates['password'] = null;
                $updates['password_qrcode'] = null;

                $oldImage = $room->settings->password_qrcode;
                Storage::delete(str_replace('/storage/', 'public/', $oldImage));
            }

            if (isset($request->chat_enabled)) {
                $updates['chat_enabled'] = $request->chat_enabled === 'true';
            }

            if (isset($request->chat_messages_saved)) {
                $updates['chat_messages_saved'] = $request->chat_messages_saved === 'true';
            }

            if (isset($request->allow_voters_upload)) {
                $updates['allow_voters_upload'] = $request->allow_voters_upload === 'true';
            }

            $room->settings()->update($updates);

            $updatedSettings = $room->settings()->first();

            return response()->json($updatedSettings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
