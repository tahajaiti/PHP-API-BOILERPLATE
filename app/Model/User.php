<?php

namespace app\Model;

class User extends Model
{
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

}