<?php

namespace App\Controllers;

use App\Models\SubGenreModel;
use App\Controllers\BaseController;

class SubGenreController extends BaseController
{
    public function updateSubGenre()
    {
        $subGenreId = $this->request->getPost('subgenreId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');
        
        $subGenreModel = new SubGenreModel();
        $validationErrors = $subGenreModel->validateSubGenreRules($field, $newValue);
    
        if ($validationErrors !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validationErrors
            ]);
        }
    
        $subGenre = $subGenreModel->find($subGenreId); 
        if (!$subGenre) {
            log_message('error', 'Sous-genre non trouvé pour ID: ' . $subGenreId);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sous-genre non trouvé pour l\'ID spécifié'
            ]);
        }
    
        $updated = $subGenreModel->updateSubGenre($subGenreId, $field, $newValue);
    
        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Échec de la mise à jour']);
        }
    }

    public function addSubgenre()
    {
        $subGenreModel = new SubGenreModel();

        $subgenreName = $this->request->getPost('subgenreName');

        $result = $subGenreModel->addSubgenre($subgenreName);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->back()->with('success', 'Le sous-genre a été ajouté avec succès.');
    }    
    public function associateSubgenreToGenre()
    {
        $subGenreId = $this->request->getPost('subgenre');
        $genreId = $this->request->getPost('genre');
    
        $subGenreModel = new SubGenreModel();
        $result = $subGenreModel->associateSubgenreToGenre($subGenreId, $genreId);
    
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }
    
        return redirect()->back()->with('success', 'Association créée avec succès.');
    }    
}
