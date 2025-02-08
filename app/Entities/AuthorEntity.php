<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class AuthorEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'authorName'   => null,
        'comment' => null,
        'status'    => null,
    ];  
}