<?php

namespace App\Repositories\Interfaces;

use App\DTOS\UserDtos\CreateUserDto;
use App\DTOS\UserDtos\UpdateUserDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getUserByEmail(string $email): User;
    public function getUserByCpf(string $cpf): User;
    public function createUser(CreateUserDto $data): User;
    public function updateUser(User $user, UpdateUserDto $data): User;
    public function deleteUser(User $user): void;
}
