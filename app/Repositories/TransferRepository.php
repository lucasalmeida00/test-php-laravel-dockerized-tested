<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TransferRepositoryInterface;
use App\DTOS\Transfer\TransferDto;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class TransferRepository implements TransferRepositoryInterface {
    public function createTransfer(TransferDto $data): Transfer
    {
        DB::beginTransaction();
        try {
            $transfer = Transfer::create([
                'user_id' => $data->user_id,
                'recipient_id' => $data->recipient_id,
                'amount' => $data->amount,
            ]);
            DB::commit();
            return $transfer->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
