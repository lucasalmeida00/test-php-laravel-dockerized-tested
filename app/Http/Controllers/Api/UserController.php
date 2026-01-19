<?php

namespace App\Http\Controllers\Api;

use App\Dtos\User\CreateUserDto;
use App\Dtos\User\UpdateUserDto;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function getUser(Request $request)
    {
        $user = $this->userService
            ->getUserById($request->user()->id);
        return response()->json($user);
    }

    public function createUser(CreateUserRequest $request)
    {
        $user = $this->userService
            ->createUser(CreateUserDto::fromArray($request->validated()));

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateUser(Request $request)
    {
        $user = $this->userService
            ->updateUser($request->user(), UpdateUserDto::fromArray($request->all()));
        return response()->json($user);
    }

    public function deleteUser(Request $request)
    {
        $this->userService
            ->deleteUser($request->user()->id);
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
