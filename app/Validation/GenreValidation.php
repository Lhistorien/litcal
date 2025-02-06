<?php

namespace App\Validation;

use Config\Services;

class GenreValidation
{
    public static $GenreRules = [
        'genreName' => 'required|min_length[3]|max_length[100]|is_unique[genre.genreName]',
    ];

    public static $GenreMessages = [
        'genreName' => [
            'required' => 'Le nom du genre est requis.',
            'min_length' => 'Le nom du genre doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom du genre ne doit pas dépasser 100 caractères.',
            'is_unique' => 'Ce nom de genre est déjà utilisé.',
        ],
    ];

    public static function getRules()
    {
        return self::$GenreRules;
    }

    public static function getMessages()
    {
        return self::$GenreMessages;
    }
}
