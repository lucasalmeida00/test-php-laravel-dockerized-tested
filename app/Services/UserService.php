<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\DTOS\UserDtos\CreateUserDto;
use App\DTOS\UserDtos\UpdateUserDto;
use App\Models\User;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function getUserById(int $id): User
    {
        return $this->userRepository->getUserById($id);
    }

    public function createUser(CreateUserDto $data): User
    {
        return $this->userRepository->createUser($data);
    }

    public function updateUserAmount(User $user, float $amount): User
    {
        return $this->userRepository->updateUserAmount($user, $amount);
    }

    public function updateUser(User $user, UpdateUserDto $data): User
    {
        return $this->userRepository->updateUser($user, $data);
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->deleteUser($user);
    }

    public function getUserByEmail(string $email): User
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function getUserByCpf(string $cpf): User
    {
        return $this->userRepository->getUserByCpf($cpf);
    }
}
