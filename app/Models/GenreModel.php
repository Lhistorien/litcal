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
        if (!in_array($field, $this->allowedFields)) 
        {
            return false;
        }

        $data = 
        [
            $field => $newValue
        ];

        return $this->update($genreId, $data);
    }

    public function validateGenreRules($field, $newValue)
    {
        $validation = \Config\Services::validation();

        $rules = [];

        if ($field == 'genreName') 
        {
            $rules = EditGenreValidation::$EditGenreRules['newValue'];
        } 
        elseif ($field == 'status') 
        {
            $rules = EditGenreValidation::$EditGenreRules['status'];
        }

        $validation->setRules([$field => $rules], EditGenreValidation::$EditGenreMessages);

        if (!$validation->run([$field => $newValue])) 
        {
            return $validation->getErrors(); 
        }

        return true;
    }

    public function addGenre($genreName)
    {
        $validation = \Config\Services::validation();

        $validation->setRules(GenreValidation::getRules(), GenreValidation::getMessages());
        
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