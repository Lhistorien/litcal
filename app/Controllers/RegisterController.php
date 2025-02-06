<?php

namespace App\Controllers;

use App\Validation\RegisterValidation; // Permet d'accéder aux règles de validation
use App\Controllers\BaseController;
use App\Entities\UserEntity;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    public function register()
    {
        helper(['form']);

        if (!$this->request->is('post')) 
        {
            return view('register', ['meta_title' => 'Créer un compte']);
        }
        else
        {
            $validation = \Config\Services::validation();
            $validation->setRules(RegisterValidation::$RegistrationRules, RegisterValidation::$RegistrationMessages);

            if (!$validation->withRequest($this->request)->run())
            {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
    
            $data = 
            [
                'pseudo' => $this->request->getPost('pseudo'),
                'email' => $this->request->getPost('email'),
                'birthday' => $this->request->getPost('birthday'),
                'password' => $this->request->getPost('pwdcontrol'),
            ];
            
            $user = new UserEntity();
            $user->fill($data);
            //Modifie le mdp après l'avoir ajouté à $data grâce à une méthode se trouvant dans UserEntity
            $user->setPassword($data['password']);
    
            $userModel = new UserModel();
    
            if ($userModel->save($user)) 
            {
                return redirect()->to('/')->withInput()->with('success','Utilisateur enregistré avec succès'); 
            } 
            else 
            {
                return redirect()->back()->withInput()->with('errors', 'Je suis ici');
            }
        }
    }
}