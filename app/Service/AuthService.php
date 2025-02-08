<?php

namespace app\Service;

use app\Core\JWToken;
use app\Core\Request;
use app\Core\Validator;
use app\Model\User;
use app\Repository\Repository;
use app\Repository\UserRepository;

class AuthService extends Service
{

    public function __construct(Repository $repository)
    {
        parent::__construct($repository);
        $this->repository = new UserRepository('users');

    }

    public function login(Request $request): array|false
    {
        if (!$this->validate($request, false)) {
            return false;
        }

        $model = new User();
        $model->setEmail($request->get('email'));
        $model->setPassword($request->get('password'));

        $this->repository->setModel($model);
        $user = $this->repository->findByEmail();

        if (!$this->verifyPassword($request->get('password'), $user->getPassword())) {
            return false;
        }

        return [
            'token' => JWToken::generate($model->toArray()),
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

    protected function mapToModel(Request|array $data): User
    {
        return new User($data->all());
    }

    protected function getModelClass(): string
    {
        return User::class;
    }
}