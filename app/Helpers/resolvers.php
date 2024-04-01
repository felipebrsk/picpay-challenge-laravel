<?php

use Tymon\JWTAuth\JWT;
use App\Services\{
    UserService,
    WalletService,
    TransactionService,
    TransactionTokenService,
};

if (!function_exists('jwt')) {
    /**
     * Resolve the jwt class.
     *
     * @return \Tymon\JWTAuth\JWT
     */
    function jwt(): JWT
    {
        return resolve(JWT::class);
    }
}

if (!function_exists('userService')) {
    /**
     * Resolve the user service.
     *
     * @return \App\Services\UserService
     */
    function userService(): UserService
    {
        return resolve(UserService::class);
    }
}

if (!function_exists('transactionTokenService')) {
    /**
     * Resolve the transaction token service.
     *
     * @return \App\Services\TransactionTokenService
     */
    function transactionTokenService(): TransactionTokenService
    {
        return resolve(TransactionTokenService::class);
    }
}

if (!function_exists('transactionService')) {
    /**
     * Resolve the transaction service.
     *
     * @return \App\Services\TransactionTokenService
     */
    function transactionService(): TransactionService
    {
        return resolve(TransactionService::class);
    }
}

if (!function_exists('walletService')) {
    /**
     * Resolve the wallet service.
     *
     * @return \App\Services\WalletService
     */
    function walletService(): WalletService
    {
        return resolve(WalletService::class);
    }
}
