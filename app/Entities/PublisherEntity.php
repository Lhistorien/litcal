<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class PublisherEntity extends Entity
{
    protected $attributes = 
    [
        'id'       => null,
        'publisherName'   => null,
        'website' => null,
        'status'    => null,
    ];  
}