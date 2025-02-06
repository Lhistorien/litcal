<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SubGenreEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'subgenreName'   => null,
        'status'    => null,
    ];  
}