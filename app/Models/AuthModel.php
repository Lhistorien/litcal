<?php
namespace App\Models;

class AuthModel {
    
    public function authenticate($email,$password){
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email',$email)->first();
        
        $valid = password_verify($password, $user->password);
        if($valid)
        {
            $user->password='';
            session()->set('user',$user);
            return $user;
        }
        return null;
    }
}
