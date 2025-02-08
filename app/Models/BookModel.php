<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\BookEntity;
use App\Validation\EditBookrValidation;
use App\Validation\BookValidation;
use CodeIgniter\Database\RawSql;

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
                                serie.serieName, bookserie.volume, genre.genreName, subgenre.subgenreName')
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

    public function updateBook($bookId, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) {
            return [
                'success' => false,
                'errors' => ['Le champ est invalide']
            ];
        }

        $validationResult = $this->validateBookRules($field, $newValue, $bookId);
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }

        $data = [
            $field => $newValue
        ];

        return $this->update($bookId, $data) ? ['success' => true] : ['success' => false, 'errors' => ['Échec de la mise à jour']];
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
        // Démarrer la transaction
        $this->db->transStart();
    
        // 1. Insérer dans Book
        $bookData = [
            'title' => $data['title'],
            'publisher' => $data['publisher'],
            'publication' => $data['publication'],
            'preorder' => $data['preorder'] ?? 0,  // Valeur par défaut si non fournie
            'language' => $data['language'],
            'ISBN' => $data['ISBN'] ?? null,
            'price' => $data['price'] ?? null,
            'format' => $data['format'],
            'link' => $data['link'] ?? null,
            'description' => $data['description'],
            'cover' => $data['cover'] ?? null,
            'cover_url' => $data['cover_url'] ?? null,
            'status' => 1 // Statut par défaut
        ];
    
        $this->db->insert('Book', $bookData);
        $bookId = $this->db->insert_id(); // Récupérer l'ID du livre créé
    
        // 2. Insérer dans BookAuthor
        if (isset($data['authors'])) {
            foreach ($data['authors'] as $author) {
                $authorData = [
                    'book' => $bookId,
                    'author' => $author['author_id'],
                    'role' => 'Writer' // Rôle par défaut 'Writer'
                ];
                $this->db->insert('BookAuthor', $authorData);
            }
        }
    
        if (isset($data['actor_name']) && isset($data['actor_role'])) {
            foreach ($data['actor_name'] as $index => $actorId) {
                $actorData = [
                    'book' => $bookId,
                    'author' => $actorId, // Ici, on suppose que 'actor_name' correspond à l'ID de l'auteur ou à un identifiant associé
                    'role' => $data['actor_role'][$index] // Le rôle vient de 'actor_role' à l'index correspondant
                ];
                $this->db->insert('BookAuthor', $actorData);
            }
        }
    
        // 3. Insérer dans BookSerie si nécessaire
        if (isset($data['serie']) && isset($data['tome'])) {
            $serieData = [
                'book' => $bookId,
                'serie' => $data['serie'],  // L'ID de la série
                'volume' => $data['tome']   // Le volume du livre dans la série
            ];
            $this->db->insert('BookSerie', $serieData);
        }
    
        // 4. Insérer dans BookGenre si nécessaire
        if (isset($data['genres'])) {
            foreach ($data['genres'] as $genre) {
                $genreData = [
                    'book' => $bookId,
                    'genre' => $genre
                ];
                $this->db->insert('BookGenre', $genreData);
            }
        }
    
        // 5. Insérer dans BookSubgenre si nécessaire
        if (isset($data['subgenres'])) {
            foreach ($data['subgenres'] as $subgenre) {
                $subgenreData = [
                    'book' => $bookId,
                    'subgenre' => $subgenre
                ];
                $this->db->insert('BookSubgenre', $subgenreData);
            }
        }
    
        // 6. Finaliser la transaction
        $this->db->transComplete();
    
        // Vérifier si la transaction s'est terminée correctement
        if ($this->db->TransStatus() === FALSE) {
            // En cas d'erreur, retourner false ou gérer l'erreur
            return false;
        }
    
        // Retourner l'ID du livre créé
        return $bookId;
    }    
}