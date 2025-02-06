<?php

namespace App\Controllers;

use App\Models\LanguageModel;
use App\Controllers\BaseController;

class LanguageController extends BaseController
{
    public function updateLanguage()
    {
        $languageModel = new LanguageModel();

        $abbreviation = $this->request->getPost('abbreviation');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');

        if (!$abbreviation || !$field || !$newValue) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Données incomplètes.'
            ]);
        }

        $result = $languageModel->updateLanguage($abbreviation, $field, $newValue);

        if ($result === true) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Échec de la mise à jour', 'errors' => $result]);
        }
    }
    public function addLanguage()
    {
        $languageModel = new LanguageModel();
    
        $data = [
            'abbreviation' => $this->request->getPost('abbreviation'),
            'languageName' => $this->request->getPost('languageName')
        ];
    
        $result = $languageModel->addLanguage($data);
    
        if ($result === true) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } elseif (is_array($result)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $result
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du langage.'
            ]);
        }
    }    
}
