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
use App\Models\BookSubscriptionModel;
use App\Models\LabelSubscriptionModel;
use App\Controllers\BaseController;
use Config\Database;


class BookController extends BaseController
{
    public function index()
    {
    
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
    
        return view('books', $data);
    }      
    

    public function editBook($id)
    {
        $bookModel      = new BookModel();
        $authorModel    = new AuthorModel();
        $publisherModel = new PublisherModel();
        $languageModel  = new LanguageModel();
        $genreModel     = new GenreModel();
        $subGenreModel  = new SubGenreModel();
        $roleModel      = new RoleModel();
        $serieModel     = new SerieModel();
        
        $db = \Config\Database::connect();
        $formats = $db->table('format')->get()->getResult();
        
        $book = $bookModel->getBookById($id);
        if (!$book) {
            return redirect()->to('/books')->with('error', 'Le livre n\'existe pas');
        }
        
        $data = [
            'meta_title'    => 'Édition du livre',
            'book'          => $book,
            'listofauthors' => $authorModel->where('status', 1)->orderBy('authorName', 'ASC')->findAll(),
            'publishers'    => $publisherModel->where('status', 1)->orderBy('publisherName', 'ASC')->findAll(),
            'languages'     => $languageModel->findAll(),
            'genres'        => $genreModel->where('status', 1)->findAll(),
            'subgenres'     => $subGenreModel->where('status', 1)->findAll(),
            'roles'         => $roleModel->orderBy('roleName', 'ASC')->findAll(),
            'series'        => $serieModel->orderBy('serieName', 'ASC')->findAll(),
            'formats'       => $formats,
        ];
        
        return view('editBook', $data);
    }      

    public function updateBook()
    {
        $bookId = $this->request->getPost('bookId');
        $post   = $this->request->getPost();
        
        // Gestion de l'upload de la couverture
        $file = $this->request->getFile('cover');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('cover', $newName);
            $post['cover'] = 'cover/' . $newName;
        }
        
        $bookModel = new BookModel();
        $result = $bookModel->updateBook($bookId, $post);
        
        if (is_array($result) && isset($result['validation']) && $result['validation'] === false) {
            return redirect()->back()->withInput()->with('error', json_encode($result['errors']));
        } elseif ($result === true) {
            return redirect()->to('/books')->with('success', 'Le livre a été mis à jour avec succès.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }       

    public function addBook()
    {
        $bookModel = new BookModel();
        
        log_message('debug', 'File upload data: ' . print_r($_FILES, true));
    
        $bookData = [
            'title' => $this->request->getPost('title'),
            'publisher' => $this->request->getPost('publisher'),
            'publication' => $this->request->getPost('publication'),
            'preorder' => $this->request->getPost('preorder', FILTER_VALIDATE_BOOLEAN) ?? 0,
            'language' => $this->request->getPost('language'),
            'isbn' => preg_replace('/\D/', '', $this->request->getPost('isbn')) ?? null,
            'price' => $this->request->getPost('price') ?? null,
            'format' => $this->request->getPost('format'),
            'link' => $this->request->getPost('link') ?? null,
            'description' => $this->request->getPost('description'),
            'author' => $this->request->getPost('author') ?? [],
            'actor_name' => $this->request->getPost('actor_name') ?? [],
            'actor_role' => $this->request->getPost('actor_role') ?? [],
            'serie' => $this->request->getPost('serie'),
            'volume' => $this->request->getPost('volume'),
            'genre' => $this->request->getPost('genre') ?? [],
            'subgenre' => $this->request->getPost('subgenre') ?? [],
        ];
    
        //  Gére l'upload de l'image AVANT d'envoyer les données au modèle
        $file = $this->request->getFile('cover');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            log_message('debug', 'File name: ' . $file->getName());
            log_message('debug', 'File temporary path: ' . $file->getTempName());
    
            $validation = \Config\Services::validation();
            $validation->setRules([
                'cover' => 'uploaded[cover]|max_size[cover,4096]|is_image[cover]',
            ]);
    
            if (!$validation->withRequest($this->request)->run()) {
                log_message('debug', 'File validation failed: ' . print_r($validation->getErrors(), true));
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
    
            log_message('debug', 'File validation passed.');
    
            $newName = $file->getRandomName();
            $file->move('cover', $newName);
            $bookData['cover'] = 'cover/' . $newName;
    
            log_message('debug', 'File moved to: ' . $bookData['cover']);
        } else {
            log_message('debug', 'No file uploaded or file input is missing.');
            $bookData['cover'] = null;
        }
    
        $result = $bookModel->addBook($bookData);
    
        if (!$result['validation']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }
    
        return redirect()->back()->with('success', 'Le livre a été ajouté avec succès.');
    }
   
    public function getAuthorBooks()
    {
        $authorId = $this->request->getPost('id');

        $bookModel = new \App\Models\BookModel();
        $books = $bookModel->getBooksByAuthor($authorId);
        
        return view('components/bookAuthorModal', ['books' => $books]);
    }

    public function getBookDetails($id)
    {
        $bookModel = new \App\Models\BookModel();
        $book = $bookModel->getBookById($id);
        
        if (empty($book)) {
            return 'Livre non trouvé.';
        }
        
        // Construction du tableau des label IDs
        $labelIds = [];
        
        // Pour les auteurs
        if (!empty($book['authors'])) {
            foreach ($book['authors'] as $author) {
                if (isset($author['id'])) {
                    $labelIds[] = 'AU' . $author['id'];
                }
            }
        }
        // Pour l'éditeur
        if (isset($book['publisherId'])) {
            $labelIds[] = 'PU' . $book['publisherId'];
        }
        // Pour la série
        if (isset($book['serieId'])) {
            $labelIds[] = 'SE' . $book['serieId'];
        }
        // Pour les genres
        if (!empty($book['genres'])) {
            foreach ($book['genres'] as $genre) {
                if (isset($genre['id'])) {
                    $labelIds[] = 'GE' . $genre['id'];
                }
            }
        }
        // Pour les sous-genres
        if (!empty($book['subgenres'])) {
            foreach ($book['subgenres'] as $subgenre) {
                if (isset($subgenre['id'])) {
                    $labelIds[] = 'SG' . $subgenre['id'];
                }
            }
        }
        
        // Supprimer les doublons
        $labelIds = array_unique($labelIds);
        
        // Récupérer les labels correspondants via le LabelModel seulement si le tableau n'est pas vide
        if (!empty($labelIds)) {
            $labelModel = new \App\Models\LabelModel();
            $labels = $labelModel->getLabelsByIds($labelIds);
        } else {
            $labels = [];
        }
        
        // Enrichir chaque label avec l'état de souscription pour l'utilisateur connecté
        $userId = session()->get('user_id');
        if ($userId) {
            $subscriptionModel = new LabelSubscriptionModel();
            foreach ($labels as $label) {
                $label->subscribed = $subscriptionModel->isSubscribed($userId, $label->id);
            }
        }
        
        $data = [
            'book'   => $book,
            'labels' => $labels,
        ];
        
            return view('components/bookDetailsContent', $data);
    }  
    
    public function deactivateBook($id)
    {
        $bookModel = new \App\Models\BookModel();
        if($bookModel->update($id, ['status' => 0])){
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['success' => false]);
        }
    }    

    public function subscribeBook($id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!session()->get('is_logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour suivre un livre.'
                ]);
            } else {
                return redirect()->to('/auth')->with('errors', 'Vous devez être connecté pour suivre un livre.');
            }
        }
        
        $userId = session()->get('user_id');
        $subscriptionModel = new BookSubscriptionModel();
        $result = $subscriptionModel->toggleSubscription($id, $userId);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        } else {
            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            } else {
                return redirect()->back()->with('errors', $result['message']);
            }
        }
    }       
}
