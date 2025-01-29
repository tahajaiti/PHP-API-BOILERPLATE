<?php

namespace app\Service;

use app\Core\Request;
use app\Core\Service;
use app\Model\User;

class AuthService extends Service
{

    protected function validate(Request $data): void
    {
        // TODO: Implement validate() method.
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