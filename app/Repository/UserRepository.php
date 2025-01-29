<?php

namespace app\Repository;

use app\Core\Database;
use app\Core\Model;
use app\Core\Repository;
use app\Model\User;
use Exception;

class UserRepository extends Repository
{

    /**
     * @throws Exception
     */
    public function findByEmail() : ?Model
    {
        $data = $this->model->toArray();
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $user = $this->db->fetch($sql, $data);

        return $user ? new ($this->getModelClass())($user) : null;
    }

}