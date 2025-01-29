<?php

namespace app\Model;

use app\Core\Database;
use app\Core\Model;
use Exception;

class User extends Model
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;


    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = ucfirst($name);
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

}