<?php

namespace app\Model;

use app\Core\Database;
use Exception;

class User
{
    private Database $pdo;
    private int $id;
    private string $name;
    private string $email;
    private string $password;

    /**
     * @param  int $id
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(string $name, string $email, string $password,int $id = 0)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setEmail($email);
        $this->setPassword($password);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * @throws Exception
     */
}