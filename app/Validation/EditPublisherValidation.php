<?php

namespace App\Validation;

class EditPublisherValidation
{
    public static $EditPublisherRules = [
        'newValue' => 'required|min_length[3]|max_length[50]|is_unique[publisher.publisherName]',
        'status'   => 'required|in_list[0,1]',
        'website'  => 'permit_empty|min_length[5]|max_length[75]|valid_url'
    ];

    public static $EditPublisherMessages = [
        'newValue' => [
            'required'    => 'La valeur est requise.',
            'min_length'  => 'La valeur doit contenir au moins 3 caractères.',
            'max_length'  => 'La valeur ne doit pas dépasser 50 caractères.',
            'is_unique'   => 'Ce nom existe déjà.',
        ],
        'status' => [
            'required'    => 'Le statut est requis.',
            'in_list'     => 'Le statut doit être 0 ou 1.',
        ],
        'website' => [
            'min_length'  => 'L\'URL doit contenir au moins 5 caractères.',
            'max_length'  => 'L\'URL ne doit pas dépasser 75 caractères.',
            'valid_url'   => 'L\'URL n\'est pas valide.',
        ]
    ];
}
