<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Services\UserService;

class AuthController extends Controller
{
    public function __construct(public UserService $user_service){
        //
    }

    public function register(RegisterRequest $request)
    {
        return $this->user_service->register($request->validated());
    }

    public function login(LoginRequest $request)
    {
        return $this->user_service->login($request->validated());
    }
}
