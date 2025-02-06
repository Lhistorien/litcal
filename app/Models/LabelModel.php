<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\LabelEntity;

class LabelModel extends Model
{
    protected $table      = 'label';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = LabelEntity::class;
    
    protected $allowedFields = [
        'labelName',
        'status',
    ];

    public function convertToEntity(array $data): LabelEntity
    {
        return new LabelEntity($data);
    }
}
