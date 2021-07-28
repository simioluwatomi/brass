<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function __invoke()
    {
        return $this->respond(['user' => new UserResource(auth()->user())]);
    }
}
