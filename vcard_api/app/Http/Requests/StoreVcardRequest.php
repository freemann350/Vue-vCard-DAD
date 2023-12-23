<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVcardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => [
                'required',
                'string',
                'max:9',
                'starts_with:9',
                Rule::unique('vcards')
            ],
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string',
            'confirmation_code' => [
                'required',
                'numeric',
                'digits:3'
            ],
            'photo_url' => 'nullable|string|max:255',
            'base64ImagePhoto' => 'nullable|string',
            'deletePhotoOnServer' => 'nullable|boolean'
        ];
    }
}
