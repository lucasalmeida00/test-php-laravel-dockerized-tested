<?php

namespace App\Services;

use App\DTOS\Transfer\TransferDto;
use App\Models\Permission;
use App\Models\Transfer;
use App\Models\User;
use App\Repositories\Interfaces\TransferRepositoryInterface;
use RuntimeException;

class TransferService
{
    public function __construct(
        private TransferRepositoryInterface $transferRepository,
        private UserService $userService
    ) {}

    public function createTransfer(TransferDto $data): Transfer
    {
        $user = $this->userService->getUserById($data->user_id);
        $recipient = $this->userService->getUserById($data->recipient_id);

        $this->validateTransfer($user, $recipient, $data->amount);

        $this->userService->updateUserAmount($user, $user->amount - $data->amount);
        $this->userService->updateUserAmount($recipient, $recipient->amount + $data->amount);

        return $this->transferRepository->createTransfer($data);
    }

    public function validateTransfer(User $user, User $recipient, float $amount): void
    {
        try {
            $this->validateCanTransfer($user);
            $this->validateCanTransferToRecipient($user, $recipient);
            $this->validateAmount($user, $recipient, $amount);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new RuntimeException($e->getMessage());
        }
    }

    private function validateCanTransfer(User $user): void
    {
        if (!$user->hasPermission(Permission::PERMISSION_CAN_TRANSFER))
            throw new RuntimeException('User not have permission to transfer funds.');
    }

    private function validateCanTransferToRecipient(User $user, User $recipient): void
    {
        if ($user->id === $recipient->id)
            throw new RuntimeException('You cannot transfer funds to yourself.');
    }

    private function validateAmount(User $user, User $recipient, float $amount): void
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
