<?php

namespace app\Repository;

use app\Core\Database;
use app\Core\Repository;
use app\Model\User;
use Exception;

class UserRepository extends Repository
{

    /**
     * @throws Exception
     */
    public function findByEmail() : array
    {
        $data = $this->extractData($this->model);
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";

        return $this->db->fetch($sql, $data) ?? [];
    }

}