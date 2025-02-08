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
        'website',
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
        elseif ($field == 'website') 
        {
            $rules = EditPublisherValidation::$EditPublisherRules['website'];
        }

        $validation->setRules([$field => $rules], EditPublisherValidation::$EditPublisherMessages);

        if (!$validation->run([$field => $newValue])) 
        {
            return $validation->getErrors(); 
        }

        return true;
    }

    public function addPublisher($publisherName, $website = null)
    {
        $validation = \Config\Services::validation();
        
        $data = ['publisherName' => $publisherName];
    
        if (!empty($website)) {
            $data['website'] = $website;
        }
    
        $validation->setRules(PublisherValidation::getRules(), PublisherValidation::getMessages());
        
        if (!$validation->run($data)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }
        
        $this->insert($data);
        
        return ['success' => true];
    }
}
