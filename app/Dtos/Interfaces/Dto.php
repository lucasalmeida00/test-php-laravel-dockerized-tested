<?php

namespace App\DTOS\Interfaces;

interface Dto
{
    public function toArray(): array;
    public static function fromArray(array $data): self;
}
