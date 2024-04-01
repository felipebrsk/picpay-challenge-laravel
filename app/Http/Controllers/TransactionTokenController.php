<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransactionTokenResource;
use App\Http\Requests\TransactionToken\TransactionTokenStoreRequest;

class TransactionTokenController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\TransactionToken\TransactionTokenStoreRequest $request
     * @return \App\Http\Resources\TransactionTokenResource
     */
    public function __invoke(TransactionTokenStoreRequest $request): TransactionTokenResource
    {
        $data = $request->validated();

        $token = DB::transaction(function () use ($data) {
            return transactionTokenService()->create($data);
        });

        return TransactionTokenResource::make($token);
    }
}
