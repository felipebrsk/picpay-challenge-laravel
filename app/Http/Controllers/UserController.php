<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\UserStoreRequest;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\User\UserStoreRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function store(UserStoreRequest $request): UserResource
    {
        try {
            $data = $request->validated();

            $user = DB::transaction(function () use ($data) {
                return userService()->create($data);
            });

            return UserResource::make($user->load('wallet', 'userable'));
        } catch (Exception $e) {
            logWithContext('Failed to create a new user', $e);
            throw $e;
        }
    }
}
