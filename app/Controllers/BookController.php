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
use App\Controllers\BaseController;
use App\Validation\BookValidation;
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
    $bookModel = new BookModel();

    $book = $bookModel->getBookById($id);


    if (!$book) {
        return redirect()->to('/books')->with('error', 'Le livre n\'existe pas');
    }


    return view('editBook', ['book' => $book[0], 'meta_title' => 'Édition']);
}

    public function updateBook()
    {
        $bookId = $this->request->getPost('bookId');
        $post = $this->request->getPost();

        $bookModel = new BookModel();
        
        $data = [
            'title' => $post['title'],
            'publisher' => 5,
            'price' => $post['price'],
            'language' => $post['languageAbbreviation'],
            'isbn' => $post['isbn']
        ];

        // $result = $bookModel->updateBook($bookId, $data);
        $bookModel->update('bookId', ['status'=>0]);

        // if ($result['success']) {
        //     return redirect()->to('/book/'.$bookId)->with('success', 'Le livre a été mis à jour.');
        // } else {
        //     return view('editBook', [
        //         'book' => $data,  // Passer les données à la vue
        //         'errors' => $result['errors'],  // Passer les erreurs à la vue
        //         'meta_title' => 'coucou',
        //     ]);
        // }
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
        
        if ($this->request->isAJAX()) {
            // Si la requête est AJAX, renvoyer la version pour la Home Page
            return view('components/bookDetailsContent', ['book' => $book]);
        } else {
            // Sinon, renvoyer la vue complète du modal pour books
            return view('components/bookDetailsModal', ['book' => $book]);
        }
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
        $subscriptionModel = new \App\Models\BookSubscriptionModel();
        
        // Rechercher si une souscription existe déjà pour ce livre et cet utilisateur
        $subscription = $subscriptionModel->where('book', $id)
                                          ->where('user', $userId)
                                          ->first();
        
        if ($subscription) {
            // Une souscription existe déjà, vérifier son statut
            if ($subscription['status'] == 1) {
                // Le livre est suivi (status = 1) : on passe le status à 0 pour "ne plus suivre"
                $updateData = ['status' => 0];
                $subscriptionModel->update($subscription['id'], $updateData);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Vous ne suivez plus ce livre.',
                        'action'  => 'unfollow'
                    ]);
                } else {
                    return redirect()->back()->with('success', 'Vous ne suivez plus ce livre.');
                }
            } else {
                // Le livre n'est pas suivi (status = 0) : on passe le status à 1 pour "suivre"
                $updateData = ['status' => 1];
                $subscriptionModel->update($subscription['id'], $updateData);
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Vous suivez désormais ce livre.',
                        'action'  => 'follow'
                    ]);
                } else {
                    return redirect()->back()->with('success', 'Vous suivez désormais ce livre.');
                }
            }
        } else {
            // Aucune souscription n'existe, on en crée une avec status = 1
            $data = [
                'book'   => $id,
                'user'   => $userId,
                'status' => 1
            ];
            if ($subscriptionModel->insert($data)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Vous suivez désormais ce livre.',
                        'action'  => 'follow'
                    ]);
                } else {
                    return redirect()->back()->with('success', 'Vous suivez désormais ce livre.');
                }
            } else {
                $error = $subscriptionModel->errors();
                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => 'Erreur lors du suivi du livre: ' . implode(', ', $error)
                    ]);
                } else {
                    return redirect()->back()->with('errors', 'Erreur lors du suivi du livre: ' . implode(', ', $error));
                }
            }
        }
    }     
}
