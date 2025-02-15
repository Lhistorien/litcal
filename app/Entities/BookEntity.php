<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class BookEntity extends Entity
{
    protected $attributes = [
        'id'           => null,
        'title'        => null,
        'publisher'    => null,
        'preorder'     => null,
        'publication'  => null,
        'language'     => null,
        'isbn'         => null,
        'cover'        => null,
        'format'       => null,
        'description'  => null,
        'link'         => null,
        'price'        => null,
        'comment'      => null,
        'status'       => null,
        // Attributs additionnels pour les relations
        'authors'      => [],  
        'serieId'      => null,
        'serieName'    => null,
        'genres'       => [],  
        'subgenres'    => [],  
        'labels'       => [],  
    ];    
}