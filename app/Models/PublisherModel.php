<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\PublisherEntity;
use App\Validation\EditPublisherValidation;  
use App\Validation\PublisherValidation;

class PublisherModel extends Model
{
    protected $table      = 'publisher';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = PublisherEntity::class;

    protected $allowedFields = 
    [
        'publisherName',
        'comment',
        'status',
    ];

    public function convertToEntity(array $data): PublisherEntity
    {
        return new PublisherEntity($data);
    }

    public function updatePublisher($publisherId, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) 
        {
            return false;
        }

        $data = 
        [
            $field => $newValue
        ];

        return $this->update($publisherId, $data);
    }

    public function validatePublisherRules($field, $newValue)
    {
        $validation = \Config\Services::validation();

        $rules = [];

        if ($field == 'publisherName') 
        {
            $rules = EditPublisherValidation::$EditPublisherRules['newValue'];
        } 
        elseif ($field == 'status') 
        {
            $rules = EditPublisherValidation::$EditPublisherRules['status'];
        }

        $validation->setRules([$field => $rules], EditPublisherValidation::$EditPublisherMessages);

        if (!$validation->run([$field => $newValue])) 
        {
            return $validation->getErrors(); 
        }

        return true;
    }

    public function addPublisher($publisherName)
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules(PublisherValidation::getRules(), PublisherValidation::getMessages());
        
        if (!$validation->run(['publisherName' => $publisherName])) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }
        
        $this->insert(['publisherName' => $publisherName]);
        
        return ['success' => true];
    }
    
}
