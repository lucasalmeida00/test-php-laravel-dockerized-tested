<?php

namespace App\Repositories\Interfaces;

use App\DTOS\Transfer\TransferDto;
use App\Models\Transfer;

interface TransferRepositoryInterface
{
    public function createTransfer(TransferDto $data): Transfer;
}
