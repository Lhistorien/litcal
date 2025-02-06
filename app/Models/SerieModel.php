<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\SerieEntity;

class SerieModel extends Model
{
    protected $table      = 'serie';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SerieEntity::class;

    protected $allowedFields = 
    [
        'serieName',
        'comment',
        'status',
    ];

    public function convertToEntity(array $data): SerieEntity
    {
        return new SerieEntity($data);
    }
}
