<?php

namespace app\Service;

use app\Core\Request;
use app\Core\Response;
use app\Core\Service;
use app\Core\Validator;
use app\Model\User;

class AuthService extends Service
{

    protected function validate(Request $data, bool $isCreate): bool
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ];

        if ($isCreate) {
            $rules['password'] = 'required|string|min:6';
        }

        Validator::make($data->all(), $rules);

        return empty(Validator::errors());
    }

    protected function mapToModel(Request $data): User
    {
        return new User($data->all());
    }

    protected function getModelClass(): string
    {
        return User::class;
    }
}