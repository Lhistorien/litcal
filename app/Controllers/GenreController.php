<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Controllers\BaseController;

class GenreController extends BaseController
{
    public function updateGenre()
    {
        $genreId = $this->request->getPost('genreId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');
        
        $genreModel = new GenreModel();

        $result = $genreModel->updateGenre($genreId, $field, $newValue);
    
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

    public function addGenre()
    {
        $genreModel = new GenreModel();

        $genreName = $this->request->getPost('genreName');

        $result = $genreModel->addGenre($genreName);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->back()->with('success', 'Le genre a été ajouté avec succès.');
    }    
}
