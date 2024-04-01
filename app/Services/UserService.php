<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService extends AbstractService
{
    /**
     * The user repository.
     *
     * @var \App\Repositories\UserRepository
     */
    protected $repository = UserRepository::class;
}
