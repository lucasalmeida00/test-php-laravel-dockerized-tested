<?php

namespace App\DTOS;

interface Dto
{
    public function toArray(): array;
    public static function fromArray(array $data): self;
}
