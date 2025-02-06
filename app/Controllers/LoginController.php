<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function login()
    {
        helper(['form']);

        // Log the request method
        log_message('debug', 'Request method: ' . $this->request->getMethod());

        if ($this->request->getMethod() === 'post') {
            // Log the POST data
            log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));

            // Get the form data
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $csrfToken = $this->request->getPost(csrf_token()); // Get the CSRF token from the request

            // Log the CSRF token for debugging
            log_message('debug', 'CSRF Token: ' . $csrfToken);

            // Log the email and password for debugging
            log_message('debug', 'Login attempt - Email: ' . $email);
            log_message('debug', 'Login attempt - Password: ' . $password);

            // Validate the input
            if (!$this->validate([
                'email' => 'required|valid_email',
                'password' => 'required'
            ])) {
                log_message('debug', 'Validation failed for email: ' . $email);
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => 'Email ou mot de passe incorrect.'
                ]);
            }

            // Fetch the user from the database
            $model = new \App\Models\UserModel();
            $user = $model->where('email', $email)->first();

            if ($user) {
                log_message('debug', 'User found - Email: ' . $user->email);
                log_message('debug', 'Stored hashed password: ' . $user->password);

                // Verify the password
                if (password_verify($password, $user->password)) {
                    log_message('debug', 'Password verification succeeded for email: ' . $email);

                    // Set the user session
                    session()->set(['user' => $user]);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Connexion réussie !',
                        'pseudo' => $user->pseudo
                    ]);
                } else {
                    log_message('debug', 'Password verification failed for email: ' . $email);
                }
            } else {
                log_message('debug', 'User not found for email: ' . $email);
            }

            return $this->response->setJSON([
                'success' => false,
                'errors' => 'Email ou mot de passe incorrect.'
            ]);
        }

        // Log if the request method is not POST
        log_message('debug', 'Invalid request method: ' . $this->request->getMethod());

        return $this->response->setJSON([
            'success' => false,
            'errors' => 'Requête invalide.'
        ]);
    }

    public function logout()
    {
        session()->remove('user');
        return redirect()->to('/')->with('success', 'Déconnexion réussie');
    }
}
