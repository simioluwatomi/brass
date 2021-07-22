<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends ApiController
{
    public function store(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password ?? null)) {
            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        $token = $user->createToken(config('auth.token.name'));

        return $this->respondCreated([
            'user'       => new UserResource($user),
            'token'      => $token->plainTextToken,
            'expires_in' => now()->diffInSeconds(now()->addRealMinutes(config('sanctum.expiration'))),
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondNoContent();
    }
}
