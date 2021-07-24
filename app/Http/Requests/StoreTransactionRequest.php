<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Rules\Pin;
use App\Rules\SufficientAccountBalance;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $account = Account::whereNumber($this->input('debit_account_number'))->first();

        return $account && auth()->id() === $account->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'debit_account_number'      => ['required', 'numeric', 'digits:10'],
            'credit_account_number' => ['required', 'numeric', 'digits:10'],
            'credit_account_name'   => ['required', 'string'],
            'credit_bank_code'      => ['required', 'numeric', 'exists:banks,code'],
            'amount'                     => [
                'required',
                'numeric',
                'gt:0',
                new SufficientAccountBalance('number', $this->input('debit_account_number'))
            ],
            'description'                => ['nullable', 'string', 'max:255'],
            'pin'                        => ['required', 'numeric', 'digits:4', new Pin()],
            'remember'                   => ['boolean'],
            'reference'      => ['required', 'unique:transaction_entries,reference'],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function prepareForValidation()
    {
        $this->merge(['amount' => convertAmountToBaseUnit($this->input('amount'))]);
    }
}
