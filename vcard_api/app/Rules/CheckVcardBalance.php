<?php

namespace App\Rules;

use App\Models\Vcard;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckVcardBalance implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vcard = auth()->user();
        $vcard = Vcard::findOrFail($vcard->id);
        // Check if the user's account balance is sufficient
        if (!($vcard && $vcard->max_debit >= $value)) {
            $fail("Value exceeds maximum debit allowed");
        }

        if (!($vcard && $vcard->balance >= $value)) {
            $fail("Insufficient account balance for transaction.");
        }
    }
}
