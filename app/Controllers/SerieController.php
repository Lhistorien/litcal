<?php

namespace App\Controllers;

use App\Models\SerieModel;
use App\Models\BookModel;
use App\Controllers\BaseController;

class SerieController extends BaseController
{
    public function index()
    {
        $serieModel = new SerieModel();
        
        $data['series'] = $serieModel->where('status', 1)
                                      ->orderBy('serieName', 'ASC')
                                      ->findAll();
        $data['meta_title'] = 'Espace Séries';
    
        return view('series', $data);
    }   
    
    public function updateSerie()
    {
        $serieModel = new SerieModel();

        $serieId = $this->request->getPost('serieId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');

        $validationResult = $serieModel->validateSerieRules($field, $newValue);
        
        if ($validationResult !== true) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validationResult
            ]);
        }

        $updated = $serieModel->updateSerie($serieId, $field, $newValue);

        if ($updated) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Échec de la mise à jour'
            ]);
        }
    }

    public function addSerie()
    {
        $serieModel = new SerieModel();
    
        $serieName = $this->request->getPost('serieName');
        $comment = $this->request->getPost('comment') ?? null; 
    
        $result = $serieModel->addSerie($serieName, $comment);
    
        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }
    
        return redirect()->to('/dashboard#series')->with('success', 'L\'auteur a été ajouté avec succès.');
    }     
    // Affiche les livres d'une série
    public function getSerieDetails($id)
    {
        $serieModel = new SerieModel();
        $serie = $serieModel->find($id);
        
        if (empty($serie)) {
            return 'Série non trouvée.';
        }
        
        $bookModel = new BookModel();
        $books = $bookModel->getBooksBySerie($id);
        
        // Transforme les valeurs non numériques en noms complets
        foreach ($books as $book) {
            if ($book->volume == 'I') {
                $book->volume = 'Intégrale';
            } elseif ($book->volume == 'HS') {
                $book->volume = 'Hors-Série';
            }
        }
        
        return view('components/bookSerieModal', ['serie' => $serie, 'books' => $books]);
    }    
}
