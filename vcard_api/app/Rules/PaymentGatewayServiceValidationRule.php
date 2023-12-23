<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
class PaymentGatewayServiceValidationRule implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $field;
    protected $gatewayResponse;

    public function __construct($parameter)
    {
        $this->gatewayResponse = $parameter;
    }

    public function passes($attribute, $value)
    {
        $this->field = $attribute;
        /*
        if (request('type') == 'C') {
            $apiEndpoint = 'https://dad-202324-payments-api.vercel.app/api/credit';
        } else {
            $apiEndpoint = 'https://dad-202324-payments-api.vercel.app/api/debit';
        }
            
        $requestData = [
            'type' => request('payment_type'),
            'reference' => request('payment_reference'),
            'value' => (float) request('value'),
        ];
        */
        $response = $this->gatewayResponse;
        if ($response['message'] == 'credit registered' || $response['message'] == 'debit registered' )
            return true;

        if (($response['message'] == 'invalid value' || $response['message'] == 'payment limit exceeded') && $attribute == "value")
            return false;

        if ($response['message'] == 'invalid type' && $attribute == "payment_type")
            return false;

        if (($response['message'] == 'invalid reference' || $response['message'] == 'payment reference not accepted') && $attribute == "payment_reference")
            return false;    

        if ($response['message'] == 'invalid request object format')
            return false;  

        return true;
    }

    public function message()
    {
        switch ($this->field) {
            case 'value':
                switch (request('payment_type')) {
                    case 'MBWAY':
                        return 'The value is invalid. Maximum 50€';
                    case 'PAYPAL':
                        return 'The value is invalid. Maximum 100€';
                    case 'IBAN':
                        return 'The value is invalid. Maximum 1000€';
                    case 'MB':
                        return 'The value is invalid. Maximum 500€';
                    case 'VISA':
                        return 'The value is invalid. Maximum 200€';
                }
                break;
            case 'payment_reference':
                return 'The payment reference is invalid';
            case 'payment_type':
                return 'The payment type is invalid';
            default:
                return 'The external API validation failed.';
        }
    }
}
