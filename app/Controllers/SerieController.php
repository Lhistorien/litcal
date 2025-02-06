<?php

namespace App\Controllers;

use App\Models\SerieModel;


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
}