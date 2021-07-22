<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_type' => ['required', 'exists:account_types,id'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'business_name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telephone' => ['required', 'string', 'unique:users,telephone'],
            'password' => ['required', Password::defaults()],
            'pin' => ['required',  'digits:4'],
            'terms' => ['accepted'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareForValidation()
    {
        try {
            $telephone = phone($this->input('telephone'))->formatE164();
        } catch (\Exception $exception) {
            throw ValidationException::withMessages(['telephone' => 'The telephone is invalid']);
        }

        $this->merge(['telephone' => $telephone]);
    }
}
