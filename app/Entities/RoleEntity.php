<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class RoleEntity extends Entity
{
    protected $attributes = 
    [
        'id'             => null,
        'roleName'       => null,
    ];  
}