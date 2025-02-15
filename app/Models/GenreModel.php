<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\GenreEntity;
use App\Validation\EditGenreValidation;
use App\Validation\GenreValidation;

class GenreModel extends ValidateFieldModel
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

        $validationResult = $this->validateGenre($field, $newValue, $genreId);
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }

        $data = [
            $field => $newValue
        ];

        return $this->update($genreId, $data) ? ['success' => true] : ['success' => false, 'errors' => ['Échec de la mise à jour']];
    }

    // Utilisation de l'héritage de ValidateFieldModel pour pouvoir utiliser la même méthode pour les sous-genres et les genres
    public function validateGenre($field, $newValue, $genreId = null)
    {
        return $this->validateField(EditGenreValidation::$EditGenreRules, EditGenreValidation::$EditGenreMessages, $field, $newValue, $genreId);
    }

    public function addGenre($genreName)
    {
        $validation = \Config\Services::validation();
        $validation->setRules(\App\Validation\GenreValidation::getRules(), \App\Validation\GenreValidation::getMessages());
    
        if (!$validation->run(['genreName' => $genreName])) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }
    
        $this->insert(['genreName' => $genreName]);
    
        return ['success' => true];
    }    
}
