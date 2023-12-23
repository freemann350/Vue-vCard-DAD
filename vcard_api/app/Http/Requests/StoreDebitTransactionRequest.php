<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckVcardBalance;

class StoreDebitTransactionRequest extends FormRequest
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
            'vcard' => [
                'required',
                'exists:vcards,phone_number,deleted_at,NULL',
                'exists:vcards,phone_number,blocked,0',
                'starts_with:9'
            ],
            'type' => 'required|in:D',
            'value' => [
                'required',
                'numeric',
                new CheckVcardBalance()
            ],
            'payment_type' => 'required|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'payment_reference' => 'required|different:vcard',
            'category_id' => 'nullable|numeric|exists:categories,id',
            'description' => 'nullable|string',
        ];
    }
}
