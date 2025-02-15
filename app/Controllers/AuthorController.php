<?php

namespace App\Controllers;

use App\Models\AuthorModel;
use App\Models\LabelSubscriptionModel;
use App\Controllers\BaseController;


class AuthorController extends BaseController
{
    public function index()
    {
        $authorModel = new AuthorModel();
        
        $data['authors'] = $authorModel->where('status', 1)
                                       ->orderBy('authorName', 'ASC')
                                       ->findAll();
        $data['meta_title'] = 'Espace Auteurs';
    
        return view('authors', $data);
    }    

    public function updateAuthor()
    {
        $authorModel = new AuthorModel();

        $authorId = $this->request->getPost('authorId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');

        $validationResult = $authorModel->validateAuthorRules($field, $newValue);
        
        if ($validationResult !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validationResult
            ]);
        }

        $updated = $authorModel->updateAuthor($authorId, $field, $newValue);

        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Échec de la mise à jour'
            ]);
        }
    }

    public function addAuthor()
    {
        $authorModel = new AuthorModel();
    
        $authorName = $this->request->getPost('authorName');
        $comment = $this->request->getPost('comment') ?? null; 
    
        $result = $authorModel->addAuthor($authorName, $comment);
    
        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }
    
        return redirect()->to('/dashboard#authors')->with('success', 'L\'auteur a été ajouté avec succès.');
    } 
    public function subscribeAuthorLabel()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Vous devez être connecté.'
            ]);
        }
    
        $labelId = $this->request->getPost('label');
        if (!$labelId) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Label manquant.'
            ]);
        }
    
        $model = new LabelSubscriptionModel();
        $result = $model->toggleSubscription($userId, $labelId);
    
        if ($result['success']) {
            return $this->response->setJSON($result);
        } else {
            return $this->response->setStatusCode(500)->setJSON($result);
        }
    }

    public function checkAuthorSubscription()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(403)->setJSON(['subscribed' => false]);
        }
    
        $labelId = $this->request->getPost('label');
        if (!$labelId) {
            return $this->response->setStatusCode(400)->setJSON(['subscribed' => false]);
        }
    
        $model = new LabelSubscriptionModel();
        $subscribed = $model->isSubscribed($userId, $labelId);
    
        return $this->response->setJSON(['subscribed' => $subscribed]);
    }    
}
