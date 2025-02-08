<?php

namespace App\Controllers;

use App\Models\AuthorModel;
use App\Controllers\BaseController;

class AuthorController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) 
        {
            return redirect()->to('/auth')->with('errors', 'Vous devez être connecté.');
        }
    
        $allowedRoles = ['Administrator', 'Contributor'];
        if (!in_array(session()->get('user_role'), $allowedRoles)) {
            return redirect()->to('/')->with('errors', 'Accès refusé.');
        }
    
        $authorModel = new \App\Models\AuthorModel();
        $authors = $authorModel->findAll(); 
    
        $data = [
            'meta_title' => 'Éditeur d\'auteurs',
            'title' => 'Éditeur d\'auteurs',
            'authors' => $authors, 
        ];
    
        return view('authorEditor', $data);
    }
    
    public function updateAuthor()
    {
        $authorId = $this->request->getPost('authorId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');
        
        $authorModel = new AuthorModel();

        $result = $authorModel->updateAuthor($authorId, $field, $newValue);
    
        if ($result['success']) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $result['errors']
            ]);
        }
    }    

    public function addAuthor()
    {
        $authorModel = new AuthorModel();

        $authorName = $this->request->getPost('authorName');

        $result = $authorModel->addAuthor($authorName);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->back()->with('success', 'L\'auteur a été ajoutée avec succès.');
    }    
}
