<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\SubGenreEntity;
use \App\Validation\EditSubGenreValidation;
use \App\Validation\SubgenreValidation;

class SubGenreModel extends Model
{
    protected $table      = 'subgenre';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SubGenreEntity::class;
    
    protected $allowedFields = 
    [
        'subgenreName',
        'status',
    ];

    public function convertToEntity(array $data): SubGenreEntity
    {
        return new SubGenreEntity($data);
    }

    private function validateSubGenre($data, $field)
    {
        $validation = \Config\Services::validation();
    
        if ($field === 'subgenreName') {
            $rules = SubgenreValidation::getRules();
            $messages = SubgenreValidation::getMessages();
        } elseif (isset(EditSubGenreValidation::$EditSubGenreRules[$field])) {
            $rules = [$field => EditSubGenreValidation::$EditSubGenreRules[$field]];
            $messages = isset(EditSubGenreValidation::$EditSubGenreMessages[$field])
                ? [$field => EditSubGenreValidation::$EditSubGenreMessages[$field]]
                : [];
        } else {
            return ['error' => "Aucune règle définie pour le champ '$field'"];
        }
    
        $validation->setRules($rules, $messages);
    
        if (!$validation->run($data)) {
            return $validation->getErrors();
        }
    
        return true;
    }

    public function addSubgenre($subgenreName)
    {
        $validationResult = $this->validateSubGenre(['subgenreName' => $subgenreName], 'subgenreName');
        
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }
        
        $this->insert(['subgenreName' => $subgenreName]);

        return ['success' => true];
    }

    public function updateSubGenre($subGenreId, $field, $newValue)
    {
        if (empty($subGenreId) || !is_numeric($subGenreId)) {
            log_message('error', 'ID du sous-genre invalide');
            return false;
        }
    
        if (!in_array($field, $this->allowedFields)) {
            log_message('error', 'Champ invalide: ' . $field);
            return false;
        }
    
        $validationResult = $this->validateSubGenre([$field => $newValue], $field);
        if ($validationResult !== true) {
            return $validationResult;
        }
        
        $data = [$field => $newValue];
    
        log_message('debug', 'Mise à jour du sous-genre (ID: ' . $subGenreId . ') avec les données: ' . json_encode($data));
    
        $updated = $this->where('id', $subGenreId)->set($data)->update();
    
        if (!$updated) {
            log_message('error', 'Échec de la mise à jour dans la base de données. Affecté: ' . $this->affectedRows());
            return false;
        }
    
        return true;
    }    

    public function getAllSubgenres()
    {
        return $this->findAll();
    }

    public function getGenresForSubgenre($subGenreId)
    {
        return $this->select('GROUP_CONCAT(genre.genreName SEPARATOR ", ") as genres')
                    ->join('GenreSubgenre', 'GenreSubgenre.subgenre = subgenre.id', 'left')
                    ->join('genre', 'GenreSubgenre.genre = genre.id', 'left')
                    ->where('subgenre.id', $subGenreId)
                    ->groupBy('subgenre.id')
                    ->findAll();  
    }

    public function getSubgenresWithGenres()
    {
        $subgenres = $this->findAll();
        $result = [];
    
        foreach ($subgenres as $subgenre) {
            $genres = $this->getGenresForSubgenre($subgenre->id);  
    
            $genresList = !empty($genres) ? $genres[0]->genres : '';  
    
            $subgenreWithGenres = clone $subgenre;  
            $subgenreWithGenres->genres = $genresList;
    
            $result[] = $subgenreWithGenres;
        }
    
        return $result;
    }

    public function associateSubgenreToGenre($subgenreId, $genreId)
    {
        if (empty($subgenreId) || empty($genreId) || !is_numeric($subgenreId) || !is_numeric($genreId)) {
            return ['success' => false, 'message' => 'IDs invalides.'];
        }
    
        $exists = $this->db->table('GenreSubgenre')
                           ->where(['subgenre' => $subgenreId, 'genre' => $genreId])
                           ->countAllResults();
    
        if ($exists > 0) {
            return ['success' => false, 'message' => 'Cette association existe déjà.'];
        }
    
        $this->db->table('GenreSubgenre')->insert([
            'subgenre' => $subgenreId,
            'genre' => $genreId
        ]);
    
        return ['success' => true];
    }    
}
