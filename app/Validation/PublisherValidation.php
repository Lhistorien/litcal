<?php

namespace App\Validation;

class PublisherValidation
{
    public static $PublisherRules = [
        'publisherName' => 'required|min_length[3]|max_length[100]|is_unique[publisher.publisherName]',
        'website'       => 'permit_empty|min_length[5]|max_length[75]|valid_url'
    ];

    public static $PublisherMessages = [
        'publisherName' => [
            'required'   => 'Le nom de l\'éditeur est requis.',
            'min_length' => 'Le nom de l\'éditeur doit contenir au moins 3 caractères.',
            'max_length' => 'Le nom de l\'éditeur ne doit pas dépasser 100 caractères.',
            'is_unique'  => 'Ce nom d\'éditeur est déjà utilisé.'
        ],
        'website' => [
            'min_length' => 'L\'URL doit contenir au moins 5 caractères.',
            'max_length' => 'L\'URL ne doit pas dépasser 75 caractères.',
            'valid_url'  => 'L\'URL n\'est pas valide.'
        ]
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
