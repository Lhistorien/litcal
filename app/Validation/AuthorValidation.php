<?php

namespace App\Validation;

use Config\Services;

class AuthorValidation
{
    public static $AuthorRules = [
        'authorName' => 'required|min_length[3]|max_length[100]|is_unique[author.authorName]',
    ];

    public static $AuthorMessages = [
        'authorName' => [
            'required' => 'Le nom de la série est requis.',
            'min_length' => 'Le nom de l\'auteur doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom de l\'auteur ne doit pas dépasser 100 caractères.',
            'is_unique' => 'Cet auteur existe déjà.',
        ],
    ];

    public static function getRules()
    {
        return self::$AuthorRules;
    }

    public static function getMessages()
    {
        return self::$AuthorMessages;
    }
}
