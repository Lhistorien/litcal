<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\SerieEntity;
use App\Validation\EditSerieValidation;
use App\Validation\SerieValidation;

class SerieModel extends Model
{
    protected $table      = 'serie';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SerieEntity::class;

    protected $allowedFields = 
    [
        'serieName',
        'status',
    ];

    public function convertToEntity(array $data): SerieEntity
    {
        return new SerieEntity($data);
    }

    public function updateSerie($serieId, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) {
            return [
                'success' => false,
                'errors' => ['Le champ est invalide']
            ];
        }

        $validationResult = $this->validateSerieRules($field, $newValue, $serieId);
        if ($validationResult !== true) {
            return [
                'success' => false,
                'errors' => $validationResult
            ];
        }

        $data = [
            $field => $newValue
        ];

        return $this->update($serieId, $data) ? ['success' => true] : ['success' => false, 'errors' => ['Échec de la mise à jour']];
    }

    public function validateSerieRules($field, $newValue, $serieId = null)
    {
        $validation = \Config\Services::validation();
    
        $rules = [];
    
        if ($field == 'serieName') {
            $rules = EditSerieValidation::$EditSerieRules['newValue'];
            $rules = str_replace('{serieId}', $serieId, $rules);  
        } elseif ($field == 'status') {
            $rules = EditSerieValidation::$EditSerieRules['status'];
        }
    
        $validation->setRules([$field => $rules], EditSerieValidation::$EditSerieMessages);
    
        if (!$validation->run([$field => $newValue])) {
            return $validation->getErrors();
        }
    
        return true;
    }

    public function addSerie($serieName)
    {
        $validation = \Config\Services::validation();
        $validation->setRules(SerieValidation::getRules(), SerieValidation::getMessages());

        if (!$validation->run(['serieName' => $serieName])) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }

        $this->insert(['serieName' => $serieName]);

        return ['success' => true];
    }
}