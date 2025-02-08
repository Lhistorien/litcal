<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\PublisherModel;
use App\Models\LanguageModel;
use App\Models\GenreModel;
use App\Models\SubGenreModel;
use App\Models\AuthorModel;
use App\Models\RoleModel;
use App\Models\SerieModel;
use App\Controllers\BaseController;
use Config\Database;

class BookController extends BaseController
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
    
        $bookModel = new BookModel();
        $publisherModel = new PublisherModel();
        $languageModel = new LanguageModel();
        $genreModel = new GenreModel();
        $subGenreModel = new SubGenreModel();
        $authorModel = new AuthorModel();
        $roleModel = new RoleModel();
        $serieModel = new SerieModel();

        $db = Database::connect();
        $formats = $db->table('format')->get()->getResult();
    
        $books = $bookModel->getBooksWithPublisherNameAndAuthors(); 
        $publishers = $publisherModel->where('status', 1)->orderBy('publisherName', 'ASC')->findAll(); 
        $languages = $languageModel->findAll();
        $genres = $genreModel->where('status', 1)->findAll(); 
        $subgenres = $subGenreModel->where('status', 1)->findAll(); 
        $authors = $authorModel->where('status', 1)->orderBy('authorName', 'ASC')->findAll(); 
        $roles = $roleModel->orderBy('roleName', 'ASC')->findAll();
        $series = $serieModel->orderBy('serieName', 'ASC')->findAll();
    
        $data = [
            'meta_title' => 'Éditeur de livres',
            'title' => 'Éditeur de livres',
            'books' => $books,  
            'publishers' => $publishers, 
            'languages' => $languages,
            'genres' => $genres,
            'subgenres' => $subgenres,
            'listofauthors' => $authors,
            'roles' => $roles,
            'formats' => $formats,
            'series' => $series,
        ];
    
        return view('bookEditor', $data);
    }      
    
    public function updateBook()
    {
        $bookId = $this->request->getPost('bookId');
        $field = $this->request->getPost('field');
        $newValue = $this->request->getPost('newValue');
        
        $bookModel = new BookModel();

        $result = $bookModel->updateBook($bookId, $field, $newValue);
    
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

    public function addBook()
    {
        $bookModel = new BookModel();

        $bookName = $this->request->getPost('bookName');

        $result = $bookModel->addBook($bookName);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->back()->with('success', 'Le livre a été ajoutée avec succès.');
    }    
}
