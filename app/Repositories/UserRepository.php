<?php

namespace App\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User, Customer, Shopkeeper};

class UserRepository extends AbstractRepository
{
    /**
     * The user model.
     *
     * @var \App\Models\User
     */
    protected $model = User::class;

    /**
     * Create a new user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data): User
    {
        $c = preg_replace('/\D/', '', $data['document_number']);

        if (strlen($c) == 11) {
            $data['document_type'] = 'cpf';
        } elseif (strlen($c) == 14) {
            $data['document_type'] = 'cnpj';
        }

        $forUser = Arr::except($data, ['document_type', 'document_number']);
        $forUserable = Arr::only($data, ['type', 'document_type', 'document_number']);

        $user = User::create($forUser);

        $user->userable()->associate(
            $this->createUserable($forUserable),
        )->save();

        return $user;
    }

    /**
     * Create userable for user.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function createUserable(array $data): Model
    {
        # TODO: change to services
        if ($data['type'] === User::SHOPKEEEPER_TYPE) {
            $userable = Shopkeeper::create($data);
        } else {
            $userable = Customer::create($data);
        }

        return $userable;
    }
}
