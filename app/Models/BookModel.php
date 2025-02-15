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
    //Génère la vue books 
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
            // Permet d'éviter d'ajouter 2x le même auteur avec le même rôle
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
    
        $validation = \Config\Services::validation();
        $validation->setRules(BookValidation::$BookRules, BookValidation::$BookMessages);
    
        if (!$validation->run($data)) {
            log_message('error', 'Validation failed. Errors: ' . json_encode($validation->getErrors()));
            return ['validation' => false, 'errors' => $validation->getErrors()];
        }
    
        log_message('info', 'Validation successful.');
    
        // Utilisation d'une transaction pour qu'aucune table ne soit modifiée s'il y a une erreur quelque part
        $this->db->transStart();
    
        $bookData = [
            'title' => $data['title'],
            'publisher' => $data['publisher'],
            'publication' => $data['publication'],
            'preorder' => $data['preorder'] ?? 0,
            'language' => $data['language'],
            'isbn' => isset($data['isbn']) ? preg_replace('/\D/', '', $data['isbn']) : null, // Supprime tous les caractères non numériques
            'price' => $data['price'] ?? null,
            'format' => $data['format'],
            'link' => $data['link'] ?? null,
            'description' => $data['description'],
            'cover' => $data['cover'] ?? null,
        ];
        // Rollback en cas d'erreur
        if (!$this->insert($bookData)) {
            $this->db->transRollback();
            return ['validation' => false, 'errors' => "Échec de l'ajout du livre."];
        }
    
        $bookId = $this->insertID();
    
        // Ajout aux autres tables avec roolback en cas d'erreur
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
                return false; // 
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
                if (!empty($actorId) && !empty($role)) {  
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
                return false;  
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
                return false; 
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
                return false; 
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
                return false; 
            }
        }
        return true;
    }

    public function getBookById($id)
    {
        // Permet de récupérer toutes les informations d'un livre en fonction de son ID
        $builder = $this->select('
            book.*, 
            publisher.publisherName, publisher.id as publisherId, 
            author.authorName, author.id as authorId, 
            role.roleName, 
            language.languageName, language.abbreviation, 
            serie.serieName, serie.id as serieId, 
            bookserie.volume, 
            genre.genreName, genre.id as genreId, 
            subgenre.subgenreName, subgenre.id as subgenreId, 
            book.isbn, 
            book.format
        ')
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
        
        $rows = $builder->get()->getResult();
    
        if (empty($rows)) {
            return null;
        }
    
        $first = $rows[0];
        $result = [
            'id'                   => $first->id,
            'title'                => $first->title,
            'publisherName'        => $first->publisherName,
            'publisherId'          => $first->publisherId,
            'preorder'             => $first->preorder,
            'publication'          => $first->publication,
            'languageAbbreviation' => $first->abbreviation,
            'languageName'         => $first->languageName,
            'isbn'                 => $first->isbn,
            'cover'                => $first->cover,
            'description'          => $first->description,
            'link'                 => $first->link,
            'price'                => $first->price,
            'format'               => $first->format,
            'comment'              => $first->comment,
            'status'               => $first->status,
            'authors'              => [],
            'serieName'            => $first->serieName,
            'serieId'              => $first->serieId,
            'volume'               => $first->volume,
            'genres'               => [],
            'subgenres'            => [],
        ];
    
        foreach ($rows as $row) {
            $authorEntry = [
                'name' => $row->authorName,
                'role' => $row->roleName,
                'id'   => $row->authorId,
            ];
            if (!in_array($authorEntry, $result['authors'])) {
                $result['authors'][] = $authorEntry;
            }
    
            if (!empty($row->genreName) && !in_array(['id' => $row->genreId, 'name' => $row->genreName], $result['genres'])) {
                $result['genres'][] = ['id' => $row->genreId, 'name' => $row->genreName];
            }
    
            if (!empty($row->subgenreName) && !in_array(['id' => $row->subgenreId, 'name' => $row->subgenreName], $result['subgenres'])) {
                $result['subgenres'][] = ['id' => $row->subgenreId, 'name' => $row->subgenreName];
            }
        }
    
        return $result;
    }    
    // Affiche les livres d'un auteur sur la page authors
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
    // Affiche les livres d'une série sur la page series
    public function getBooksBySerie($serieId)
    {
        $builder = $this->db->table($this->table); 
        $builder->select('Book.id, Book.title, Book.publication, BookSerie.volume');
        $builder->join('BookSerie', 'Book.id = BookSerie.book');
        $builder->where('BookSerie.serie', $serieId);
        $query = $builder->get();
        return $query->getResult();
    }
    // Génère le carousel des livres sortis ces 30 derniers jours
    public function getRecentBooks()
    {
        $date30DaysAgo = date('Y-m-d', strtotime('-30 days'));
        $today = date('Y-m-d');
    
        $books = $this->select('
                book.*, 
                publisher.publisherName, 
                publisher.id as publisherId, 
                author.authorName, 
                role.roleName, 
                language.languageName, 
                language.abbreviation, 
                serie.serieName, 
                serie.id as serieId, 
                bookserie.volume, 
                genre.genreName, 
                genre.id as genreId, 
                subgenre.subgenreName, 
                subgenre.id as subgenreId, 
                book.isbn, 
                book.format
            ')
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
            ->where('publication >=', $date30DaysAgo)
            ->where('publication <=', $today)
            ->where('book.status', 1)
            ->groupBy('book.id')
            ->findAll();
    
        return $books;
    }    
    // Génère les livres à paraître dans les 30 prochains jours
    public function getUpcomingBooks()
    {
        $today = date('Y-m-d');
        $date30DaysLater = date('Y-m-d', strtotime('+30 days'));
    
        $books = $this->select('
                book.*, 
                publisher.publisherName, 
                publisher.id as publisherId, 
                author.authorName, 
                role.roleName, 
                language.languageName, 
                language.abbreviation, 
                serie.serieName, 
                serie.id as serieId, 
                bookserie.volume, 
                genre.genreName, 
                genre.id as genreId, 
                subgenre.subgenreName, 
                subgenre.id as subgenreId, 
                book.isbn, 
                book.format
            ')
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
            ->where('publication >', $today)
            ->where('publication <=', $date30DaysLater)
            ->where('book.status', 1)
            ->groupBy('book.id')
            ->findAll();
    
        return $books;
    }    
    
    public function enrichBooksWithLabels(array $books)
    {
        foreach ($books as $book) {
            $labelIds = [];
    
            if (!empty($book->authors) && is_array($book->authors)) {
                foreach ($book->authors as $author) {
                    if (isset($author->id)) {
                        $labelIds[] = 'AU' . $author->id;
                    }
                }
            }

            if (isset($book->publisherId)) {
                $labelIds[] = 'PU' . $book->publisherId;
            }

            if (isset($book->serieId)) {
                $labelIds[] = 'SE' . $book->serieId;
            }

            if (!empty($book->genres) && is_array($book->genres)) {
                foreach ($book->genres as $genre) {
                    if (isset($genre->id)) {
                        $labelIds[] = 'GE' . $genre->id;
                    }
                }
            }

            if (!empty($book->subgenres) && is_array($book->subgenres)) {
                foreach ($book->subgenres as $subgenre) {
                    if (isset($subgenre->id)) {
                        $labelIds[] = 'SG' . $subgenre->id;
                    }
                }
            }
            
            // Supprime les doublons et ajoute la propriété 'labels' à l'objet
            $book->labels = array_unique($labelIds);
        }
        return $books;
    }
}