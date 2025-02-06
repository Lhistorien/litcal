<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuthModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (!$this->request->is('post')) 
        {
            return view('auth', ['meta_title' => 'Se connecter']);
        }
    
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
    
        $auth = new AuthModel();
        $user = $auth->authenticate($email, $password);
    
        if ($user === null) 
        {
            return redirect()->back()->with('errors', 'Email et/ou mot de passe invalide');
        }
    
        session()->set
        ([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_pseudo' => $user->pseudo,
            'user_role' => $user->role, 
            'is_logged_in' => true,
          ]);
    
        return redirect()->to('/')->with('success', 'Connexion réussie.');

    }
    
    public function logout()
    {
        setcookie("flash_success", "Déconnexion réussie", time() + 3, "/");
    
        session()->destroy();
    
        return redirect()->to('/');
    }
}