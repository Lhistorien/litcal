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
        
        $validationResult = $genreModel->validateGenreRules($field, $newValue);
        
        if ($validationResult !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validationResult
            ]);
        }

        $updated = $genreModel->updateGenre($genreId, $field, $newValue);

        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Échec de la mise à jour']);
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
