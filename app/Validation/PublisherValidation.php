<?php

namespace App\Validation;

use Config\Services;

class PublisherValidation
{
    public static $PublisherRules = [
        'publisherName' => 'required|min_length[3]|max_length[100]|is_unique[publisher.publisherName]',
    ];

    public static $PublisherMessages = [
        'publisherName' => [
            'required' => 'Le nom de l\'éditeur est requis.',
            'min_length' => 'Le nom de l\'éditeur doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom de l\'éditeur ne doit pas dépasser 100 caractères.',
            'is_unique' => 'Ce nom d\'éditeur est déjà utilisé.',
        ],
    ];

    public static function getRules()
    {
        return self::$PublisherRules;
    }

    public static function getMessages()
    {
        return self::$PublisherMessages;
    }
}
