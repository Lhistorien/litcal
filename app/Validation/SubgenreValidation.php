<?php

namespace App\Validation;

class SubgenreValidation
{
    public static $SubgenreRules = [
        'subgenreName' => 'required|min_length[3]|max_length[100]|is_unique[subgenre.subgenreName]',
    ];

    public static $SubgenreMessages = [
        'subgenreName' => [
            'required' => 'Le nom du sous-genre est requis.',
            'min_length' => 'Le nom du sous-genre doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom du sous-genre ne doit pas dépasser 100 caractères.',
            'is_unique' => 'Ce nom de sous-genre est déjà utilisé.',
        ],
    ];

    public static function getRules()
    {
        return self::$SubgenreRules;
    }

    public static function getMessages()
    {
        return self::$SubgenreMessages;
    }
}
