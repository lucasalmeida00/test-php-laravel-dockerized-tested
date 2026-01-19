<?php

use App\Http\Controllers\Api\AuthenticatorController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function(){
    return response()->json([
        'message' => 'API is running',
    ]);
});

Route::post('/transfer', [TransferController::class, 'createTransfer'])->middleware('auth:sanctum');
Route::post('/authenticate', [AuthenticatorController::class, 'login']);
Route::post('/register', [UserController::class, 'createUser']);
Route::post('/refresh-token', [AuthenticatorController::class, 'refreshToken']);
Route::post('/logout', [AuthenticatorController::class, 'logout']);
