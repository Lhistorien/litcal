<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\UserEntity;

class UserModel extends Model
{
    protected $table      = 'user';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = UserEntity::class;
    
    protected $allowedFields = [
        'pseudo',
        'email',
        'password',
        'birthday',
        'role',
        'comment',
        'status',
    ];

    public function convertToEntity(array $data): UserEntity
    {
        return new UserEntity($data);
    }
    public function saveProfileChanges($id, $data, $isAjax = false)
    {
        $validation = \Config\Services::validation();
        $validationRules = \App\Validation\EditProfileValidation::EditProfileRules($id);
        $validationMessages = \App\Validation\EditProfileValidation::$EditProfileMessages;
    
        // Si c'est une requête AJAX, ne valider que le champ modifié
        if ($isAjax) {
            $field = array_key_first($data); 
            if (!isset($validationRules[$field])) {
                return ['success' => false, 'message' => 'Champ non valide.'];
            }
    
            $validation->setRules([$field => $validationRules[$field]], [$field => $validationMessages[$field] ?? []]);
            if (!$validation->run([$field => $data[$field]])) {
                return ['success' => false, 'errors' => $validation->getErrors()];
            }
        } 
        // Sinon, validation complète pour le formulaire classique
        else {
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                return ['success' => false, 'errors' => $validation->getErrors()];
            }
        }
    
        if ($this->update($id, $data)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Mise à jour échouée.'];
        }
    }    
}
