<?php

namespace App\Validation;

class EditLanguageValidation
{
    public static $EditLanguageRules = [
        'newValue' => 'required|min_length[3]|max_length[50]|is_unique[language.languageName]',
    ];

    public static $EditLanguageMessages = [
        'newValue' => [
            'required'    => 'Le nom de la langue est requis.',
            'min_length'  => 'Le nom de la langue doit contenir au moins 3 caractères.',
            'max_length'  => 'Le nom de la langue ne doit pas dépasser 50 caractères.',
            'is_unique'   => 'Ce nom de langue existe déjà.',
        ]
    ];
}
