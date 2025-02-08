<?php

namespace App\Controllers;

use App\Models\PublisherModel;
use App\Controllers\BaseController;

class PublisherController extends BaseController
{
    public function updatePublisher()
    {
        $publisherModel = new PublisherModel();

        $publisherId = $this->request->getPost('publisherId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');

        $validationResult = $publisherModel->validatePublisherRules($field, $newValue);
        
        if ($validationResult !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validationResult
            ]);
        }

        $updated = $publisherModel->updatePublisher($publisherId, $field, $newValue);

        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Échec de la mise à jour'
            ]);
        }
    }

    public function addPublisher()
    {
        $publisherModel = new PublisherModel();
    
        $publisherName = $this->request->getPost('publisherName');
        $website = $this->request->getPost('website') ?? null; 
    
        $result = $publisherModel->addPublisher($publisherName, $website);
    
        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }
    
        return redirect()->back()->with('success', 'L\'éditeur a été ajouté avec succès.');
    }    
}
