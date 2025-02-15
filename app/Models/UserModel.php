<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\UserEntity;
use App\Validation\RegisterValidation;

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
    
    public function registerUser(array $data)
    {
        $validation = \Config\Services::validation();
        $validation->setRules(RegisterValidation::$RegistrationRules, RegisterValidation::$RegistrationMessages);
        
        if (!$validation->run($data)) {
            return $validation->getErrors();
        }
        
        $data['status'] = $data['status'] ?? 1;
        
        $user = new UserEntity();
        $user->fill($data);
        $user->setPassword($data['password']);
        
        return $this->save($user) ? true : ['error' => 'Erreur lors de l\'enregistrement'];
    }    

    public function saveProfileChanges($id, $data, $isAjax = false)
    {
        $validation = \Config\Services::validation();
        
        if ($isAjax) {
            // Scénario 1 : un admin modifie un champs du profil d'un utilisateur en ajax, on ne contrôle que celui-là
            $field = array_key_first($data);
            $rules = \App\Validation\EditProfileValidation::EditProfileRules($id);
            $messages = \App\Validation\EditProfileValidation::$EditProfileMessages;
            
            if (!isset($rules[$field])) {
                return ['success' => false, 'message' => 'Champ non valide.'];
            }
            
            $validation->setRules([$field => $rules[$field]], [$field => $messages[$field]]);
            if (!$validation->run($data)) {
                return ['success' => false, 'errors' => $validation->getErrors()];
            }
        } else {
            // Scénario 2 : l'utilisateur modifie son propre profil, on utilise toutes les règles
            $rules = \App\Validation\EditProfileValidation::EditProfileRules($id);
            $messages = \App\Validation\EditProfileValidation::$EditProfileMessages;
            
            $validation->setRules($rules, $messages);
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
