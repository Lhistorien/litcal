<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class LanguageEntity extends Entity
{
    protected $attributes = 
    [
        'abbreviation'       => null,
        'languageName'   => null,
    ];  
}