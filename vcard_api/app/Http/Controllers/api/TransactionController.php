<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreditTransactionRequest;
use App\Http\Requests\StoreDebitTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Vcard;
use App\Models\Category;
use App\Rules\PaymentGatewayServiceValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return TransactionResource::collection(Transaction::query()->where("vcard", $request->user()['username'])->orderBy('id')->get());
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    public function storeDebit(StoreDebitTransactionRequest $request)
    {
        $formData = $request->validated();
        $vcard = Vcard::findOrFail($formData['vcard']);

        $formData['date'] = date('Y-m-d');
        $formData['datetime']= date('Y-m-d  H:i:s');
        $formData['old_balance'] = $vcard->balance;
        $formData['new_balance'] = number_format((float)$vcard->balance-$formData['value'], 2, '.', '');
        
        //TRANSAÇÃO ESPELHADA (APENAS ENTRE VCARDS)
        if ($formData['payment_type'] == "VCARD") {
            $newTransaction = Transaction::create($formData);
            $formDataPair = $formData;
            $formDataPair['vcard'] = $formData['payment_reference'];
            $vcardPair = Vcard::findOrFail($formDataPair['vcard']);
            $formDataPair['type'] = "C";
            $formDataPair['pair_vcard'] = $formData['vcard'];
            $formDataPair['payment_reference'] = $formData['vcard'];
            $formDataPair['pair_transaction'] = $newTransaction->id;
            $formDataPair['old_balance'] = $vcardPair->balance;
            $formDataPair['new_balance'] = $vcardPair->balance+$formData['value'];
            $vcardPair->balance=$vcardPair->balance + $formDataPair['value'];
            //dd($formDataPair['value']);
            $pairTransaction = Transaction::create($formDataPair);

            $vcardPair->update();

            $updateTransaction['pair_vcard'] = $formData['payment_reference'];
            $updateTransaction['pair_transaction'] = $pairTransaction->id;
            $transaction = Transaction::findOrFail($newTransaction->id);
            $transaction->update($updateTransaction);
        } else {
            $apiEndpoint = 'https://dad-202324-payments-api.vercel.app/api/debit';
            
            $requestData = [
                'type' => request('payment_type'),
                'reference' => request('payment_reference'),
                'value' => (float) request('value'),
            ];
            
            
            $response = Http::post($apiEndpoint, $requestData);
            
            $gatewayValidation = new PaymentGatewayServiceValidationRule($response);
            $request->validate([
                'value' => [
                    'required',
                    $gatewayValidation,
                ],
                'payment_type' => [
                    'required',
                    $gatewayValidation,
                ],
                'payment_reference' => [
                    'required',
                    $gatewayValidation,
                    ]
                ]);
                
                $newTransaction = Transaction::create($formData);
        }

        $vcard->balance = $formData['new_balance'];
        $vcard->update();

        return new TransactionResource($newTransaction);
    }

    public function storeCredit(StoreCreditTransactionRequest $request)
    {
        $formData = $request->validated();
        
        $apiEndpoint = 'https://dad-202324-payments-api.vercel.app/api/credit';
            
        $requestData = [
            'type' => request('payment_type'),
            'reference' => request('payment_reference'),
            'value' => (float) request('value'),
        ];

        $response = Http::post($apiEndpoint, $requestData);

        $gatewayValidation = new PaymentGatewayServiceValidationRule($response);
        $request->validate([
            'value' => [
                'required',
                $gatewayValidation,
            ],
            'payment_type' => [
                'required',
                $gatewayValidation,
            ],
            'payment_reference' => [
                'required',
                $gatewayValidation,
            ]
        ]);
        
        $formData['date'] = date('Y-m-d');
        $formData['datetime']= date('Y-m-d  H:i:s');
        $vcard = Vcard::findOrFail($formData['vcard']);
        
        $formData['old_balance'] = $vcard->balance;
        $formData['new_balance'] = $vcard->balance+$formData['value'];

        $newTransaction = Transaction::create($formData);
        $vcard->balance = $formData['new_balance'];
        $vcard->update();

        return new TransactionResource($newTransaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());
        return new TransactionResource($transaction);
    }

    public function showVcardTransactions(Vcard $vcard)
    {
        $transactionQuery = Transaction::query();
        $transactionQuery->where('vcard',$vcard->phone_number);
        $transactions = $transactionQuery->get();

        return TransactionResource::collection($transactions);
    }

    private function gatewayService($data) {
        if ($data['type'] == 'C') {
            $url = "https://dad-202324-payments-api.vercel.app/api/credit";
        } else {
            $url = "https://dad-202324-payments-api.vercel.app/api/debit";
        }

        try {
            $response = Http::post($url, $data);
            $responseData = $response->json();

            return $responseData;
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function showVcardTransactionsStats(Vcard $vcard)
    {
        $transactionsByCategory = Transaction::query()
        ->select('categories.name', \DB::raw('COUNT(transactions.category_id) as count'))
        ->leftJoin('categories', 'categories.id', '=', 'transactions.category_id')
        ->where('transactions.vcard', $vcard->phone_number)
        ->groupBy('transactions.category_id', 'categories.name')
        ->get();

        return $transactionsByCategory;
    }

    public function showVcardTotalTransactions(Vcard $vcard)
    {
        $totalTransactions = Transaction::where('vcard', $vcard->phone_number)->count();

        return $totalTransactions;
    }

    public function showCountCreditTransactions(Request $request)
    {
        $countCreditTransactions = Transaction::where('type', 'C')->count();
        return $countCreditTransactions;
    }

    public function showCountDebitTransactions(Request $request)
    {
        $countDebitTransactions = Transaction::where('type', 'D')->count();
        return $countDebitTransactions;
    }
}
