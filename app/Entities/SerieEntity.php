<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SerieEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'serieName'   => null,
        'status'    => null,
    ];  
}