<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BookSubscriptionModel;
use App\Models\LabelSubscriptionModel;
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
                'id' => $id,
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
        if ($userId != session()->get('user_id')) {
            return redirect()->to('/')->with('errors', 'Accès refusé.');
        }
        
        // Récupération des abonnements aux livres
        $bookSubscriptionModel = new BookSubscriptionModel();
        $bookSubscriptions = $bookSubscriptionModel->getUserSubscriptionsWithAuthors($userId);
        
        // Récupération des abonnements aux labels
        $labelSubModel = new LabelSubscriptionModel();
        // Utilise la méthode enrichie si disponible
        if (method_exists($labelSubModel, 'getUserLabelSubscriptions')) {
            $labelSubscriptions = $labelSubModel->getUserLabelSubscriptions($userId);
        } else {
            $labelSubscriptions = $labelSubModel->where('user', $userId)
                                                ->where('status', 1)
                                                ->findAll();
        }
        
        // Enrichir chaque abonnement aux labels avec la propriété "subscribed"
        // Comme on récupère uniquement les abonnements actifs (status=1), on définit subscribed à true
        foreach ($labelSubscriptions as $sub) {
            $sub->subscribed = true;
        }
        
        $data = [
            'subscriptions'       => $bookSubscriptions,  
            'labelSubscriptions'  => $labelSubscriptions, 
            'meta_title'          => 'Mes abonnements'
        ];
        
        return view('userSubscriptions', $data);
    }         

    public function unsubscribe($subscriptionId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }
        
        $subscriptionModel = new BookSubscriptionModel();
        $subscription = $subscriptionModel->where('id', $subscriptionId)
                                          ->where('user', $userId)
                                          ->first();
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'Accès non autorisé ou abonnement inexistant.');
        }
        
        $subscriptionModel->unsubscribe($subscriptionId, $userId);
        return redirect()->back()->with('message', 'Désabonnement effectué.');
    }
    public function unsubscribeLabel($subscriptionId)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }
        
        // Créez une instance du modèle des abonnements aux labels
        $labelSubModel = new LabelSubscriptionModel();
        $subscription = $labelSubModel->where('id', $subscriptionId)
                                      ->where('user', $userId)
                                      ->first();
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'Accès non autorisé ou abonnement inexistant.');
        }
        
        // Désactive l'abonnement en mettant le status à 0
        $labelSubModel->update($subscriptionId, ['status' => 0]);
        return redirect()->back()->with('message', 'Désabonnement effectué.');
    }
    
}