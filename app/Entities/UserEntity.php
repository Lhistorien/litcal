<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'pseudo'   => null,
        'email'    => null,
        'birthday' => null,
        'password' => null,
        'status' => null,
    ];  

    public function setPassword(string $pass)
    {
        $this->attributes['password'] = password_hash($pass, PASSWORD_BCRYPT);

        return $this;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }
}