<?php

namespace App\Validation;

use App\Models\UserModel;

class CustomRules
{
    // Règle maison permettant de vérifier que l'ancien mdp est correct avant d'autoriser l'utilisateur à en changer
    public function check_old_password($str, ?string $fields = null, array $data = []): bool
    {
        // Si aucun nouveau mot de passe n'est saisi, alors la vérification de l'ancien mot de passe passe automatiquement.
        if (empty($data['newPassword'])) {
            return true;
        }
        
        // Si un nouveau mot de passe est saisi, l'ancien doit être renseigné et correct.
        if (empty($str)) {
            return false;
        }
        
        if (!isset($data['id'])) {
            return false;
        }
        
        $userModel = new UserModel();
        $user = $userModel->find($data['id']);
        
        if ($user && password_verify($str, $user->password)) {
            return true;
        }
        return false;
    }
}
