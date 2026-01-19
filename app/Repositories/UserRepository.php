<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Dtos\User\CreateUserDto;
use App\Dtos\User\UpdateUserDto;

class UserRepository implements UserRepositoryInterface
{
    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function getUserByEmail(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function getUserByCpf(string $cpf): User
    {
        return User::where('cpf', $cpf)->firstOrFail();
    }

    public function createUser(CreateUserDto $data): User
    {
        return User::create($data->toArray());
    }

    public function updateUser(User $user, UpdateUserDto $data): User
    {
        $user->update($data->toArray());
        return $user->refresh();
    }

    public function deleteUser(User $user): void
    {
        $user->deleteOrFail();
    }

    public function updateUserAmount(User $user, float $amount): User
    {
        $user->amount = $amount;
        $user->save();
        return $user->refresh();
    }
}
