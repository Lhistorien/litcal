<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;

class UserController extends BaseController
{
    public function index()
    {
        $model = new UserModel();
        $users = $model->findAll();
        
        $data = 
        [
            'meta_title' => "Users's Page",
            'title' => "Users's Page",
            'users' => $users
        ];
        
        return view('users', $data);
    }

    public function profile($id)
    {
        $model = new UserModel();
        $user = $model->find($id);
        
        if (!$user) 
        {
            return redirect()->to('/user')->with('error', "L'utilisateur n'existe pas.");
        }
        
        $data = 
        [
            'meta_title' => $user->pseudo,
            'title' => 'Vos données',
            'user' => $user
        ];
        
        return view('user', $data);
    }

    // Fonction servant à la fois à édit son propre profil en tant qu'utilisateur et tous les profils en tant qu'admin dans le dashboard.
    public function updateProfile($id = null)
    {
        helper(['form']);
        $userModel = new UserModel();
        // Vérifie s'il y a une requête AJAX (modification par un admin dans le dashboard) ou pas (modification de son profil par un utilisateur)
        if ($this->request->isAJAX()) 
        {
            $id = $this->request->getPost('id');
            $field = $this->request->getPost('field');
            $value = $this->request->getPost('value');
    
            if (!$id || !$field || $value === null) {
                return $this->response->setJSON(['success' => false, 'message' => 'Champ ou valeur manquants.']);
            }
    
            // On fait appel à une méthode de validation se trouvant dans le usermodel avec TRUE comme valeur pour indiquer que c'est une requête en AJAX
            $result = $userModel->saveProfileChanges($id, [$field => $value], true);
            return $this->response->setJSON($result);
        } 
        else 
        // Si un utilisateur modifie son propre profil
        {
            $currentUser = session()->get('user_id');
            $isAdmin = session()->get('user_role') === 'Administrator';
    
            if (!$isAdmin && $currentUser != $id) 
            {
                return redirect()->back()->with('error', "Vous n'avez pas les droits pour modifier ce profil.");
            }
    
            $data = 
            [
                'pseudo' => $this->request->getPost('pseudo'),
                'email' => $this->request->getPost('email'),
                'birthday' => $this->request->getPost('birthday'),
            ];
    
            if (!empty($this->request->getPost('newPassword'))) 
            {
                $data['password'] = password_hash($this->request->getPost('newPassword'), PASSWORD_DEFAULT);
            }
    
            // On fait appel à la même méthode qu'avec l'autre version mais on ne renvoie pas TRUE pour indiquer qu'il faut valider tout le formulaire
            $result = $userModel->saveProfileChanges($id, $data);
    
            if ($result['success']) 
            {
                return redirect()->to('/user/' . $id)->with('success', 'Profil mis à jour avec succès.');
            } 
            else 
            {
                return redirect()->back()->withInput()->with('errors', $result['errors'] ?? 'Une erreur est survenue.');
            }
        }
    }   
    public function subscriptions($userId)
    {
        // Vérifier que l'utilisateur connecté correspond à l'utilisateur dont on affiche les abonnements
        if ($userId != session()->get('user_id')) {
            return redirect()->to('/')->with('errors', 'Accès refusé.');
        }
        
        // Connexion à la base de données
        $db = \Config\Database::connect();
        
        // Préparer la requête pour récupérer les abonnements actifs de l'utilisateur
        $builder = $db->table('BookSubscription as bs');
        $builder->select('b.id, b.title, b.cover, b.publication, p.publisherName');
        $builder->join('Book as b', 'b.id = bs.book');
        $builder->join('Publisher as p', 'p.id = b.publisher');
        $builder->where('bs.user', $userId);
        $builder->where('bs.status', 1);
        
        $query = $builder->get();
        $subscriptions = $query->getResult();
        
        $data = [
            'subscriptions' => $subscriptions,
            'meta_title'    => 'Mes abonnements'
        ];
        
        return view('userSubscriptions', $data);
    }
    
}