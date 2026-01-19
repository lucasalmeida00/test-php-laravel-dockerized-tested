<?php

namespace App\Repositories\Interfaces;

use App\Dtos\User\CreateUserDto;
use App\Dtos\User\UpdateUserDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getUserById(int $id): User;
    public function getUserByEmail(string $email): User;
    public function getUserByCpf(string $cpf): User;
    public function createUser(CreateUserDto $data): User;
    public function updateUser(User $user, UpdateUserDto $data): User;
    public function updateUserAmount(User $user, float $amount): User;
    public function deleteUser(User $user): void;
}
