<?php

namespace App\Controllers;

use App\Models\SerieModel;
use App\Controllers\BaseController;

class SerieController extends BaseController
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
    
        $serieModel = new \App\Models\SerieModel();
        $series = $serieModel->findAll(); 
    
        $data = [
            'meta_title' => 'Éditeur de séries',
            'title' => 'Éditeur de séries',
            'series' => $series, 
        ];
    
        return view('serieEditor', $data);
    }
    
    public function updateSerie()
    {
        $serieId = $this->request->getPost('serieId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');
        
        $serieModel = new SerieModel();

        $result = $serieModel->updateSerie($serieId, $field, $newValue);
    
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

    public function addSerie()
    {
        $serieModel = new SerieModel();

        $serieName = $this->request->getPost('serieName');

        $result = $serieModel->addSerie($serieName);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->back()->with('success', 'La série a été ajoutée avec succès.');
    }    
}
