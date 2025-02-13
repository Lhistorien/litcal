<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\BookEntity;
use App\Validation\EditBookValidation;
use App\Validation\BookValidation;

class BookModel extends Model
{
    protected $table      = 'book';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = BookEntity::class;

    protected $allowedFields = 
    [
        'id',
        'title',
        'publisher',
        'preorder',
        'publication',
        'language',
        'isbn',
        'cover',
        'format',
        'description',
        'link',
        'price',
        'comment',
        'status',
    ];

    public function getBooksWithPublisherNameAndAuthors()
    {
        $books = $this->select('book.*, publisher.publisherName, author.authorName, role.roleName, language.languageName, language.abbreviation, 
                                serie.serieName, bookserie.volume, genre.genreName, subgenre.subgenreName, book.isbn, book.format')
                        ->join('publisher', 'publisher.id = book.publisher')
                        ->join('bookauthor', 'bookauthor.book = book.id')
                        ->join('author', 'author.id = bookauthor.author')
                        ->join('role', 'role.id = bookauthor.role')
                        ->join('language', 'language.abbreviation = book.language', 'left')  
                        ->join('bookserie', 'bookserie.book = book.id', 'left')
                        ->join('serie', 'serie.id = bookserie.serie', 'left')
                        ->join('bookgenre', 'bookgenre.book = book.id', 'left')
                        ->join('genre', 'genre.id = bookgenre.genre', 'left')
                        ->join('booksubgenre', 'booksubgenre.book = book.id', 'left')
                        ->join('subgenre', 'subgenre.id = booksubgenre.subgenre', 'left')
                        ->findAll();
    
        $result = [];
    
        foreach ($books as $book) {
            $bookId = $book->id;
    
            if (!isset($result[$bookId])) {
                $result[$bookId] = [
                    'id' => $book->id,
                    'title' => $book->title,
                    'publisherName' => $book->publisherName,
                    'preorder' => $book->preorder,
                    'publication' => $book->publication,
                    'languageAbbreviation' => $book->language,  
                    'languageName' => $book->languageName,  
                    'isbn' => $book->isbn,
                    'cover' => $book->cover,
                    'description' => $book->description,
                    'link' => $book->link,
                    'price' => $book->price,
                    'format' => $book->format,
                    'comment' => $book->comment,
                    'status' => $book->status,
                    'authors' => [],
                    'serieName' => $book->serieName,  
                    'volume' => $book->volume,
                    'genres' => [],
                    'subgenres' => [],
                ];
            }
    
            $authorEntry = [
                'name' => $book->authorName,
                'role' => $book->roleName
            ];
            // Permet d'éviter les doublons
            if (!in_array($authorEntry, $result[$bookId]['authors'])) {
                $result[$bookId]['authors'][] = $authorEntry;
            }
    
            if (!empty($book->genreName) && !in_array($book->genreName, $result[$bookId]['genres'])) {
                $result[$bookId]['genres'][] = $book->genreName;
            }
    
            if (!empty($book->subgenreName) && !in_array($book->subgenreName, $result[$bookId]['subgenres'])) {
                $result[$bookId]['subgenres'][] = $book->subgenreName;
            }
        }
    
        return array_values($result);
    }    
    
    public function convertToEntity(array $data): BookEntity
    {
        return new BookEntity($data);
    }

    public function updateBook($bookId, $data)
    {
        // var_dump(json_encode($data));
        // die();
        if (empty($data)) {
            return [
                'success' => false,
                'errors' => ['Aucune donnée à mettre à jour']
            ];
        }
    
        foreach ($data as $field => $newValue) {
            if (!in_array($field, $this->allowedFields)) {
                return [
                    'success' => false,
                    'errors' => ['Le champ ' . $field . ' est invalide']
                ];
            }
    
            // $validationResult = $this->validateBookRules($field, $newValue, $bookId);
            // if ($validationResult !== true) {
            //     return [
            //         'success' => false,
            //         'errors' => $validationResult
            //     ];
            // }
        }
    
        // Utilisation de 'where' pour préciser l'enregistrement à mettre à jour
        return $this->update($bookId, ['status' => 0]) 
            ? ['success' => true] 
            : ['success' => false, 'errors' => ['Échec de la mise à jour']];
    }
    

    public function validateBookRules($field, $newValue, $bookId = null)
    {
        $validation = \Config\Services::validation();
    
        $rules = [];
    
        if ($field == 'bookName') {
            $rules = EditBookValidation::$EditBookRules['newValue'];
            $rules = str_replace('{bookId}', $bookId, $rules);  
        } elseif ($field == 'status') {
            $rules = EditBookValidation::$EditBookRules['status'];
        }
    
        $validation->setRules([$field => $rules], EditBookValidation::$EditBookMessages);
    
        if (!$validation->run([$field => $newValue])) {
            return $validation->getErrors();
        }
    
        return true;
    }
    public function addBook($data)
    {
        log_message('debug', 'Received data: ' . json_encode($data));
    
        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules(BookValidation::$BookRules, BookValidation::$BookMessages);
    
        if (!$validation->run($data)) {
            log_message('error', 'Validation failed. Errors: ' . json_encode($validation->getErrors()));
            return ['validation' => false, 'errors' => $validation->getErrors()];
        }
    
        log_message('info', 'Validation successful.');
    
        // Démarrer la transaction
        $this->db->transStart();
    
        // Préparer les données du livre
        $bookData = [
            'title' => $data['title'],
            'publisher' => $data['publisher'],
            'publication' => $data['publication'],
            'preorder' => $data['preorder'] ?? 0,
            'language' => $data['language'],
            'isbn' => isset($data['isbn']) ? preg_replace('/\D/', '', $data['isbn']) : null,
            'price' => $data['price'] ?? null,
            'format' => $data['format'],
            'link' => $data['link'] ?? null,
            'description' => $data['description'],
            'cover' => $data['cover'] ?? null,
        ];
    
        if (!$this->insert($bookData)) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout du livre."];
        }
    
        $bookId = $this->insertID();
    
        // 🔹 Ajout des relations et vérification après chaque opération
        if (!$this->addAuthors($bookId, $data['author'] ?? [])) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout des auteurs."];
        }
    
        if (!$this->addActors($bookId, $data['actor_name'] ?? [], $data['actor_role'] ?? [])) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout des acteurs."];
        }
    
        if (!$this->addSeries($bookId, $data['serie'] ?? null, $data['volume'] ?? null)) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout de la série."];
        }
    
        if (!$this->addGenres($bookId, $data['genre'] ?? [])) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout des genres."];
        }
    
        if (!$this->addSubGenres($bookId, $data['subgenre'] ?? [])) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout des sous-genres."];
        }
    
        // Finaliser la transaction
        $this->db->transComplete();
    
        if ($this->db->transStatus() === false) {
            return ['validation' => false, 'errors' => "Une erreur s'est produite, l'ajout a été annulé."];
        }
    
        return ['validation' => true, 'bookId' => $bookId];
    }    

    private function addAuthors($bookId, $authors)
    {
        if (!empty($authors)) {
            $bookAuthors = [];
            foreach ($authors as $authorId) {
                $bookAuthors[] = [
                    'book' => $bookId,
                    'author' => $authorId,
                    'role' => 1,
                ];
            }
            if (!$this->db->table('bookauthor')->insertBatch($bookAuthors)) {
                return false; // ⛔ Retourner false en cas d'échec
            }
        }
        return true;
    }

    private function addActors($bookId, $actorNames, $actorRoles)
    {
        if (!empty($actorNames) && !empty($actorRoles)) {
            $bookActors = [];
            foreach ($actorNames as $key => $actorId) {
                $role = $actorRoles[$key] ?? null;
                if (!empty($actorId) && !empty($role)) {  // On évite d'insérer des valeurs vides
                    $bookActors[] = [
                        'book' => $bookId,
                        'author' => $actorId,
                        'role' => $role,
                    ];
                } else {
                    log_message('error', "Données invalides pour l'auteur: $actorId avec rôle: " . json_encode($role));
                }
            }
            if (!$this->db->table('bookauthor')->insertBatch($bookActors)) {
                return false; // ⛔ Retourner false en cas d'échec
            }
        }
        return true;
    }

    private function addSeries($bookId, $serie, $volume)
    {
        if (!empty($serie) && isset($volume)) {
            $bookSeries = [
                'book' => $bookId,
                'serie' => $serie,
                'volume' => $volume,
            ];
            if (!$this->db->table('bookserie')->insert($bookSeries)) {
                return false; // ⛔ Retourner false en cas d'échec
            }
        }
        return true;
    }

    private function addGenres($bookId, $genres)
    {
        if (!empty($genres)) {
            $bookGenres = [];
            foreach ($genres as $genreId) {
                $bookGenres[] = [
                    'book' => $bookId,
                    'genre' => $genreId,
                ];
            }
            if (!$this->db->table('bookgenre')->insertBatch($bookGenres)) {
                return false; // ⛔ Retourner false en cas d'échec
            }
        }
        return true;
    }

    private function addSubGenres($bookId, $subGenres)
    {
        if (!empty($subGenres)) {
            $bookSubGenres = [];
            foreach ($subGenres as $subGenreId) {
                $bookSubGenres[] = [
                    'book' => $bookId,
                    'subgenre' => $subGenreId,
                ];
            }
            if (!$this->db->table('booksubgenre')->insertBatch($bookSubGenres)) {
                return false; // ⛔ Retourner false en cas d'échec
            }
        }
        return true;
    }

    public function getBookById($id)
    {
        $builder = $this->select('book.*, 
            publisher.publisherName, 
            author.authorName, 
            role.roleName, 
            language.languageName, 
            language.abbreviation, 
            serie.serieName, 
            bookserie.volume, 
            genre.genreName, 
            subgenre.subgenreName, 
            book.isbn, 
            book.format')
            ->join('publisher', 'publisher.id = book.publisher')
            ->join('bookauthor', 'bookauthor.book = book.id')
            ->join('author', 'author.id = bookauthor.author')
            ->join('role', 'role.id = bookauthor.role')
            ->join('language', 'language.abbreviation = book.language', 'left')
            ->join('bookserie', 'bookserie.book = book.id', 'left')
            ->join('serie', 'serie.id = bookserie.serie', 'left')
            ->join('bookgenre', 'bookgenre.book = book.id', 'left')
            ->join('genre', 'genre.id = bookgenre.genre', 'left')
            ->join('booksubgenre', 'booksubgenre.book = book.id', 'left')
            ->join('subgenre', 'subgenre.id = booksubgenre.subgenre', 'left')
            ->where('book.id', $id);
        
        // Récupérer toutes les lignes correspondant à ce livre
        $rows = $builder->get()->getResult();
    
        if (empty($rows)) {
            return null;
        }
    
        // Le premier résultat contient les informations communes du livre
        $first = $rows[0];
        $result = [
            'id'                    => $first->id,
            'title'                 => $first->title,
            'publisherName'         => $first->publisherName,
            'preorder'              => $first->preorder,
            'publication'           => $first->publication,
            'languageAbbreviation'  => $first->abbreviation,  // ou $first->language selon votre besoin
            'languageName'          => $first->languageName,
            'isbn'                  => $first->isbn,
            'cover'                 => $first->cover,
            'description'           => $first->description,
            'link'                  => $first->link,
            'price'                 => $first->price,
            'format'                => $first->format,
            'comment'               => $first->comment,
            'status'                => $first->status,
            'authors'               => [],
            'serieName'             => $first->serieName,
            'volume'                => $first->volume,
            'genres'                => [],
            'subgenres'             => [],
        ];
    
        // Parcourir toutes les lignes pour récupérer tous les auteurs et autres champs pouvant varier
        foreach ($rows as $row) {
            $authorEntry = [
                'name' => $row->authorName,
                'role' => $row->roleName
            ];
            if (!in_array($authorEntry, $result['authors'])) {
                $result['authors'][] = $authorEntry;
            }
            
            if (!empty($row->genreName) && !in_array($row->genreName, $result['genres'])) {
                $result['genres'][] = $row->genreName;
            }
            
            if (!empty($row->subgenreName) && !in_array($row->subgenreName, $result['subgenres'])) {
                $result['subgenres'][] = $row->subgenreName;
            }
        }
    
        return $result;
    }

    public function getBooksByAuthor($authorId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('Book.id, Book.title, Book.publication, Role.roleName');
        $builder->join('BookAuthor', 'Book.id = BookAuthor.book');
        $builder->join('Role', 'BookAuthor.role = Role.id');
        $builder->where('BookAuthor.author', $authorId);
        $query = $builder->get();
        return $query->getResult();
    }

    public function getBooksBySerie($serieId)
    {
        $builder = $this->db->table($this->table); 
        $builder->select('Book.id, Book.title, Book.publication, BookSerie.volume');
        $builder->join('BookSerie', 'Book.id = BookSerie.book');
        $builder->where('BookSerie.serie', $serieId);
        $query = $builder->get();
        return $query->getResult();
    }

    public function getRecentBooks()
    {
        $date30DaysAgo = date('Y-m-d', strtotime('-30 days'));
        $today = date('Y-m-d');
        
        return $this->where('publication >=', $date30DaysAgo)
                    ->where('publication <=', $today)
                    ->where('status', 1)
                    ->findAll();
    }
    
    public function getUpcomingBooks()
    {
        $today = date('Y-m-d');
        $date30DaysLater = date('Y-m-d', strtotime('+30 days'));
        
        return $this->where('publication >', $today)
                    ->where('publication <=', $date30DaysLater)
                    ->where('status', 1)
                    ->findAll();
    }
}