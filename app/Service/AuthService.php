<?php

namespace app\Service;

use app\Core\JWToken;
use app\Core\Request;
use app\Core\Response;
use app\Core\Service;
use app\Core\Validator;
use app\Model\User;

class AuthService extends Service
{

    public function login(Request $request): array|false
    {
        if (!$this->validate($request, false)) {
            return false;
        }

        $model = new User();
        $model->setEmail($request->get('email'));
        $model->setPassword($request->get('password'));

        if (!$this->verifyPassword($request->get('password'), $model->getPassword())) {
            return false;
        }

        return [
            'token' => JWToken::generate(),
        ];
    }

    private function verifyPassword($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

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