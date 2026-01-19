<?php

namespace App\Dtos\User;

use App\Dtos\Interfaces\Dto;

class CreateUserDto implements Dto
{
    public function __construct(
        public string $name,
        public string $cpf,
        public string $email,
        public string $password,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['cpf'],
            $data['email'],
            $data['password'],
        );
    }
}
