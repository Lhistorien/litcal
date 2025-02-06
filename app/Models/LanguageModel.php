<?php

namespace App\Models;

use CodeIgniter\Model;
use \App\Entities\LanguageEntity;
use App\Validation\EditLanguageValidation;
use App\Validation\LanguageValidation;
use Config\Services;

class LanguageModel extends Model
{
    protected $table      = 'language';
    protected $primaryKey = 'abbreviation';
    protected $useAutoIncrement = false;
    protected $returnType = LanguageEntity::class;
    
    protected $allowedFields = [
        'languageName',
    ];

    public function convertToEntity(array $data): LanguageEntity
    {
        return new LanguageEntity($data);
    }

    public function updateLanguage($abbreviation, $field, $newValue)
    {
        if (!in_array($field, $this->allowedFields)) 
        {
            return false;
        }

        $validation = Services::validation();
        $validation->setRules(['newValue' => EditLanguageValidation::$EditLanguageRules['newValue']]);

        if (!$validation->run(['newValue' => $newValue])) 
        {
            return $validation->getErrors();
        }

        return $this->update($abbreviation, [$field => $newValue]);
    }
    public function addLanguage($data)
    {
        $validation = Services::validation();
        $validation->setRules
        (
            LanguageValidation::$LanguageRules,
            LanguageValidation::$LanguageMessages
        );
    
        if (!$validation->run($data)) 
        {
            return $validation->getErrors();
        }
    
        return $this->insert($data) ? true : false;
    }
}
