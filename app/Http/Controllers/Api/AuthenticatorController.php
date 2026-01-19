<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AuthenticatorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticateRequest;

class AuthenticatorController extends Controller
{
    public function __construct(private AuthenticatorService $authenticatorService)
    {
    }

    public function login(AuthenticateRequest $request)
    {
        $data = $request->validated();
        $user = $this->authenticatorService->login($data['email'], $data['password']);
        return response()->json([
            'user' => $user['user'],
            'token' => $user['token'],
        ]);
    }

    public function logout()
    {
        $this->authenticatorService->logout();
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function refreshToken(Request $request)
    {
        $token = $this->authenticatorService->refreshToken($request->token);
        return response()->json([
            'token' => $token,
        ]);
    }
}
