<?php

namespace app\Repository;

use app\Model\Model;
use Exception;

class UserRepository extends Repository
{

    public function findAll(): array
    {
        $sql = "SELECT id, name, email FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    /**
     * @throws Exception
     */
    public function findByEmail() : ?Model
    {
        $sql = "SELECT * FROM $this->table WHERE email = :email";
        $user = $this->db->fetch($sql, [':email' => $this->model->getEmail()]);

        return $user ? new ($this->getModelClass())($user) : null;
    }

}