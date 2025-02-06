<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class LabelEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'labelName'   => null,
        'status'    => null,
    ];  
}