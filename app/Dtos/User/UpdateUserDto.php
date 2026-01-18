<?php

namespace App\DTOS\UserDtos;

use App\DTOS\Dto;

class UpdateUserDto implements Dto
{
    public function __construct(
        public string $name,
        public string $cpf,
        public string $email,
        public string $password,
        public float $amount,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'cpf' => $this->cpf,
            'email' => $this->email,
            'password' => $this->password,
            'amount' => $this->amount,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['cpf'],
            $data['email'],
            $data['password'],
            $data['amount'],
        );
    }
}
