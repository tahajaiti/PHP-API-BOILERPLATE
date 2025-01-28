<?php

namespace app\repository;

use app\Core\Database;
use app\Core\Repository;
use app\Model\User;
use Exception;

class UserRepository extends Repository
{

    public function __construct(User $user) {
        $this->table = 'users';
        $this->db = Database::getInstance();
        $this->model = $user;
    }

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