<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'min:3', 'max:20'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];

        if ($this->route()->named('login.store')) {
            $rules = [
                'username' => ['required'],
                'password' => ['required'],
            ];
        }

        if ($this->route()->named('user.settings.update')) {
            $rules = [
                'avatar' => ['image', 'nullable'],
                'first_name' => ['max:255'],
                'last_name' => ['max:255'],
                'phone' => ['max:20'],
                'address' => ['max:255'],
            ];
        }

        return $rules;
    }
}
