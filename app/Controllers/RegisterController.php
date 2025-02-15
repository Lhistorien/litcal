<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\AuthModel;

class RegisterController extends BaseController
{
    public function register()
    {
        helper(['form']);

        if (!$this->request->is('post')) {
            return view('register', ['meta_title' => 'Créer un compte']);
        }
        
        $data = [
            'pseudo'    => $this->request->getPost('pseudo'),
            'email'     => $this->request->getPost('email'),
            'birthday'  => $this->request->getPost('birthday'),
            'password'  => $this->request->getPost('password'),
            'pwdcontrol'=> $this->request->getPost('pwdcontrol'),
        ];
        
        $userModel = new UserModel();
        $result = $userModel->registerUser($data);

        // Connecte automatiquement l'utilisateur si l'enregistrement s'est bien déroulé
        if ($result === true) 
        {
            $authModel = new AuthModel();
            $user = $authModel->authenticate($data['email'], $data['password']);
            
            if ($user) {
                return redirect()->to('/')->with('success', 'Utilisateur enregistré et connecté avec succès');
            } else {
                // Ne devrait pas arriver puisque l'enregistrement vient d'être vérifié avec le formulaire mais on ne sait jamais...
                return redirect()->to('/login')->with('error', 'Erreur lors de la connexion automatique.');
            }
        } else {
            return redirect()->back()->withInput()->with('errors', $result);
        }
    }
}
