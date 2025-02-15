<?php
namespace App\Models;

use App\Models\UserModel;

class AuthModel {
    
    public function authenticate($email, $password) {
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        
        // Vérifie que l'utilisateur n'est pas désactivé (status=0) et que le mot de passe est correct
        if ($user && password_verify($password, $user->password) && $user->status == 1) {
            $user->password = '';
            session()->set([
                'is_logged_in' => true,      
                'user'         => $user,
                'user_id'      => $user->id,
                'user_role'    => $user->role,  
                'user_email'   => $user->email,
                'user_pseudo'  => $user->pseudo,
            ]);
            return $user;
        }
        return null;
    }
}
