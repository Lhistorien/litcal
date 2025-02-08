<?php

namespace App\Validation;

use Config\Services;

class SerieValidation
{
    public static $SerieRules = [
        'serieName' => 'required|min_length[3]|max_length[100]|is_unique[serie.serieName]',
    ];

    public static $SerieMessages = [
        'serieName' => [
            'required' => 'Le nom de la série est requis.',
            'min_length' => 'Le nom de la série doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom de la série ne doit pas dépasser 100 caractères.',
            'is_unique' => 'Ce nom de série est déjà utilisé.',
        ],
    ];

    public static function getRules()
    {
        return self::$SerieRules;
    }

    public static function getMessages()
    {
        return self::$SerieMessages;
    }
}
