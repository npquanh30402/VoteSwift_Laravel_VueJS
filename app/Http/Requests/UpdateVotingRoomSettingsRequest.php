<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVotingRoomSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $room = $this->route('room');

        return $room->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invitation_only' => 'nullable|boolean',
            'wait_for_voters' => 'nullable|boolean',
            'public_visibility' => 'nullable|boolean',
            'require_password' => 'nullable|string',
            'chat_enabled' => 'nullable|boolean',
            'chat_messages_saved' => 'nullable|boolean',
            'allow_voters_upload' => 'nullable|boolean',
            'realtime_enabled' => 'nullable|boolean',
            'voters_can_see_realtime_results' => 'nullable|boolean',
            'minimum_age' => 'nullable|integer',
            'maximum_age' => 'nullable|integer',
        ];
    }
}
