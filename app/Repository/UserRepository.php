<?php

namespace app\Repository;

use app\Model\Model;

class UserRepository extends Repository
{

    public function findAll(): array
    {
        $this->query->select(['id', 'name', 'email']);

        return $this->db->fetchAll($this->query->getQuery());
    }


    public function findByEmail(): ?Model
    {
        $this->query->select()->where('email', '=', $this->model->getEmail());
        $user = $this->db->fetch($this->query->getQuery(), [':email' => $this->model->getEmail()]);


        return $user ? new ($this->getModelClass())($user) : null;
    }

}