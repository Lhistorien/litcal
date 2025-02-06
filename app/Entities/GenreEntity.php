<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class GenreEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'genreName'   => null,
        'status'    => null,
    ];  
}