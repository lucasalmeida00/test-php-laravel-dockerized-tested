<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\TransferRepositoryInterface;
use RuntimeException;

class TransferService
{
    public function __construct(private TransferRepositoryInterface $transferRepository) {}

    public function validateTransfer(User $user, float $amount): void
    {
        $userAmount = $user->amount;
        $transferAmount = $amount;

        if ($transferAmount <= 0)
            throw new RuntimeException('The transfer amount must be greater than zero.');

        if ($userAmount < $transferAmount)
            throw new RuntimeException('Insufficient balance to complete the transfer.');

        return;
    }
}
