<?php

namespace App\DTOS\Transfer;

use App\DTOS\Interfaces\Dto;

class TransferDto implements Dto
{
    public function __construct(
        public int $user_id,
        public int $recipient_id,
        public float $amount,
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'recipient_id' => $this->recipient_id,
            'amount' => $this->amount,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['recipient_id'],
            $data['amount'],
        );
    }
}
