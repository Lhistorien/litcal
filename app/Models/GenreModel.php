<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\GenreEntity;
use App\Validation\EditGenreValidation;
use App\Validation\GenreValidation;

class GenreModel extends Model
{
    protected $table      = 'genre';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = GenreEntity::class;

    protected $allowedFields = 
    [
        'genreName',
        'status',
    ];

    public function convertToEntity(array $data): GenreEntity
    {
        return new GenreEntity($data);
    }

    public function updateGenre($genreId, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) {
            return [
                'success' => false,
                'errors' => ['Le champ est invalide']
            ];
        }

        $validationResult = $this->validateGenreRules($field, $newValue, $genreId);
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }

        $data = [
            $field => $newValue
        ];

        // Mise à jour
        return $this->update($genreId, $data) ? ['success' => true] : ['success' => false, 'errors' => ['Échec de la mise à jour']];
    }

    public function validateGenreRules($field, $newValue, $genreId = null)
    {
        $validation = \Config\Services::validation();
    
        $rules = [];
    
        if ($field == 'genreName') {
            $rules = EditGenreValidation::$EditGenreRules['newValue'];
            $rules = str_replace('{genreId}', $genreId, $rules);  
        } elseif ($field == 'status') {
            $rules = EditGenreValidation::$EditGenreRules['status'];
        }
    
        $validation->setRules([$field => $rules], EditGenreValidation::$EditGenreMessages);
    
        if (!$validation->run([$field => $newValue])) {
            return $validation->getErrors();
        }
    
        return true;
    }

    public function addGenre($genreName)
    {
        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules(GenreValidation::getRules(), GenreValidation::getMessages());

        if (!$validation->run(['genreName' => $genreName])) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }

        // Insertion
        $this->insert(['genreName' => $genreName]);

        return ['success' => true];
    }
}
