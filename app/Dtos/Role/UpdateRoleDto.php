<?php

namespace App\DTOS\Role;

use App\DTOS\Interfaces\Dto;

class UpdateRoleDto implements Dto
{
    public function __construct(
        public string $name,
        public string $description,
        public array $permissions,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => $this->permissions,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'],
            $data['permissions'],
        );
    }
}
