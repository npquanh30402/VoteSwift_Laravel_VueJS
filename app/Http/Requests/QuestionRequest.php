<?php

namespace App\Http\Requests;

use App\Services\HelperService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'question_image' => HelperService::convertNullStringToNull($this->input('question_image'))
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
            'question_title' => 'required|string|min:10|max:100',
            'question_description' => 'nullable',
            'question_image' => 'nullable',
            'allow_multiple_votes' => 'nullable',
            'allow_skipping' => 'nullable',
        ];
    }
}
