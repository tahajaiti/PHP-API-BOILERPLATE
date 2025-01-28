<?php

namespace app\repository;

use app\Core\Repository;
use Exception;

class UserRepository extends Repository
{

    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    /**
     * @throws Exception
     */
    public function findByEmail(string $email) : array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";

        return $this->db->fetch($sql, ["email" => $email]) ?? [];
    }

}