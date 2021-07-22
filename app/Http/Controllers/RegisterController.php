<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\UserResource;
use App\Models\Account;
use App\Models\User;
use App\Options\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone'),
            'password' => Hash::make($request->input('password')),
            'pin' => Hash::make($request->input('pin'))
        ]);

        $account = Account::create([
            'user_id' => $user->id,
            'type_id' => $request->input('account_type'),
            'name' => $request->input('business_name'),
            'number' => generateNubanAccountNumber(),
            'status' => AccountStatus::ACTIVE
        ]);

        return $this->respondCreated([
            'user' => new UserResource($user),
            'account' => new AccountResource($account)
        ]);
    }
}
