<?php

namespace App\Http\Requests;

use App\Services\HelperService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'candidate_image' => HelperService::convertNullStringToNull($this->input('candidate_image'))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'candidate_title' => 'required|string|min:10|max:100',
            'candidate_description' => 'nullable',
            'candidate_image' => 'nullable',
        ];
    }
}
