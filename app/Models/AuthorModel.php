<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\AuthorEntity;
use App\Validation\EditAuthorValidation;
use App\Validation\AuthorValidation;

class AuthorModel extends Model
{
    protected $table      = 'author';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = AuthorEntity::class;

    protected $allowedFields = 
    [
        'authorName',
        'status',
    ];

    public function convertToEntity(array $data): AuthorEntity
    {
        return new AuthorEntity($data);
    }

    public function updateAuthor($authorId, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) {
            return [
                'success' => false,
                'errors' => ['Le champ est invalide']
            ];
        }

        $validationResult = $this->validateAuthorRules($field, $newValue, $authorId);
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }

        $data = [
            $field => $newValue
        ];

        return $this->update($authorId, $data) ? ['success' => true] : ['success' => false, 'errors' => ['Échec de la mise à jour']];
    }

    public function validateAuthorRules($field, $newValue, $authorId = null)
    {
        $validation = \Config\Services::validation();
    
        $rules = [];
    
        if ($field == 'authorName') {
            $rules = EditAuthorValidation::$EditAuthorRules['newValue'];
            $rules = str_replace('{authorId}', $authorId, $rules);  
        } elseif ($field == 'status') {
            $rules = EditAuthorValidation::$EditAuthorRules['status'];
        }
    
        $validation->setRules([$field => $rules], EditAuthorValidation::$EditAuthorMessages);
    
        if (!$validation->run([$field => $newValue])) {
            return $validation->getErrors();
        }
    
        return true;
    }

    public function addAuthor($authorName)
    {
        $validation = \Config\Services::validation();
        $validation->setRules(AuthorValidation::getRules(), AuthorValidation::getMessages());

        if (!$validation->run(['authorName' => $authorName])) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }

        $this->insert(['authorName' => $authorName]);

        return ['success' => true];
    } 
}