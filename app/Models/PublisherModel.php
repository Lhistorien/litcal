<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\PublisherEntity;
use App\Validation\EditPublisherValidation;  
use App\Validation\PublisherValidation;

class PublisherModel extends Model
{
    protected $table            = 'publisher';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = PublisherEntity::class;
    
    protected $allowedFields = [
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
        if (!in_array($field, $this->allowedFields)) {
            return false;
        }

        $data = [
            $field => $newValue
        ];

        return $this->update($publisherId, $data);
    }

    public function validatePublisherRules($field, $newValue, $publisherId = null)
    {
        $validation = \Config\Services::validation();

        $rules    = EditPublisherValidation::$EditPublisherRules;
        $messages = EditPublisherValidation::$EditPublisherMessages;

        // Si un ID est fourni et que le champ concerné est "publisherName", remplace le placeholder {id} par l'ID réel pour la validation
        if ($publisherId !== null && isset($rules['publisherName'])) {
            $rules['publisherName'] = str_replace('{id}', $publisherId, $rules['publisherName']);
        }

        // Vérifie que le champ possède une règle de validation.
        if (!isset($rules[$field])) {
            return ['error' => 'Aucune règle de validation définie pour ce champ.'];
        }

        // Configure la validation pour ce champ uniquement.
        $validation->setRules([$field => $rules[$field]], [$field => $messages[$field]]);

        if (!$validation->run([$field => $newValue])) {
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
