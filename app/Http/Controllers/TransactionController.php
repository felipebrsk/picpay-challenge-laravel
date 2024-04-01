<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TransactionResource::collection(
            transactionService()->allForUser()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Transaction\TransactionStoreRequest $request
     * @return \App\Http\Resources\TransactionResource
     */
    public function store(TransactionStoreRequest $request): TransactionResource
    {
        $data = $request->validated();

        $transaction = DB::transaction(function () use ($data) {
            return transactionService()->create($data);
        });

        return TransactionResource::make($transaction);
    }

    /**
     * Cancel a transaction.
     *
     * @param string $id
     * @return \App\Http\Resources\TransactionResource
     */
    public function cancel(string $id): TransactionResource
    {
        $transaction = DB::transaction(function () use ($id) {
            return transactionService()->cancel($id);
        });

        return TransactionResource::make($transaction);
    }
}
