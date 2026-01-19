<?php

namespace App\Http\Controllers\Api;

use App\DTOS\Transfer\TransferDto;
use Illuminate\Http\Request;
use App\Services\TransferService;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;

class TransferController extends Controller
{
    public function __construct(private TransferService $transferService) {}

    public function createTransfer(TransferRequest $request)
    {
        $data = $request->validated();

        $transfer = $this
            ->transferService
            ->createTransfer(TransferDto::fromArray([
                'user_id' => $data['payer'],
                'recipient_id' => $data['payee'],
                'amount' => $data['value'],
            ]));

        return response()->json([
            'transfer' => $transfer,
        ]);
    }
}
