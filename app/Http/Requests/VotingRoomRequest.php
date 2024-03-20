<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VotingRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (auth()->check()) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'room_name' => 'required',
            'room_description' => 'nullable',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ];
    }
}
