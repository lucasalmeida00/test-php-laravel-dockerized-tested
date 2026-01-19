<?php

namespace App\Services\Consumers;

use Illuminate\Support\Facades\Http;

class UtilsApi
{
    public function canTransfer()
    {
        $validate = Http::utilsApi()
            ->get("/v2/authorize")
            ->json();

        return $validate['status'] === 'success';
    }

    public function notifyTransfer()
    {
        $validate =  Http::utilsApi()
            ->post("/v1/notify")
            ->json();

        return !(isset($validate['status']) && $validate['status'] === 'error');
    }
}
