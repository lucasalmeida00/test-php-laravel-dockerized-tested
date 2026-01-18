<?php

namespace App\DTOS\Permission;

use App\DTOS\Interfaces\Dto;

class CreatePermissionDto implements Dto
{
    public function __construct(
        public string $name,
        public ?string $description,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description ?? null,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? null,
        );
    }
}
