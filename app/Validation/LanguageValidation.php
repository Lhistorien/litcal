<?php

namespace App\Validation;

class LanguageValidation
{
    public static $LanguageRules = [
        'abbreviation' => 'required|alpha|exact_length[2]|is_unique[language.abbreviation]',
        'languageName' => 'required|string|min_length[2]|max_length[50]|is_unique[language.languageName]',
    ];

    public static $LanguageMessages = [
        'abbreviation' => [
            'required' => 'L’abréviation est requise.',
            'alpha' => 'L’abréviation ne peut contenir que des lettres.',
            'exact_length' => 'L’abréviation doit contenir exactement 2 caractères.',
            'is_unique' => 'Cette abréviation est déjà utilisée.',
        ],
        'languageName' => [
            'required' => 'Le nom de la langue est requis.',
            'string' => 'Le nom de la langue doit être une chaîne de caractères.',
            'min_length' => 'Le nom de la langue doit contenir au moins 2 caractères.',
            'max_length' => 'Le nom de la langue ne doit pas dépasser 50 caractères.',
            'is_unique' => 'Ce nom de langue est déjà utilisé.',
        ],
    ];
}