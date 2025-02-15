<?php

namespace App\Models;

use CodeIgniter\Model;

abstract class ValidateFieldModel extends Model
{
    // Méthode générique servant aussi bien à GenreModel qu'à SubGenreModel
    protected function validateField(array $rules, array $messages, string $field, $newValue, $id = null)
    {
        $validation = \Config\Services::validation();
        
        if ($id !== null && isset($rules[$field])) {
            $rules[$field] = str_replace('{id}', $id, $rules[$field]);
        }
        
        if (!isset($rules[$field])) {
            return ['error' => "Aucune règle de validation définie pour le champ '$field'"];
        }
        
        $validation->setRules([$field => $rules[$field]], [$field => $messages[$field]]);
        
        if (!$validation->run([$field => $newValue])) {
            return $validation->getErrors();
        }
        
        return true;
    }
}