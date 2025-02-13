<?php

namespace App\Controllers;

use App\Models\AuthorModel;
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
    
        return redirect()->back()->with('success', 'L\'auteur a été ajouté avec succès.');
    }    
}
