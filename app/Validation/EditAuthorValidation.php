<?php

namespace App\Validation;

class EditAuthorValidation
{
    public static $EditAuthorRules = [
        'newValue' => 'required|min_length[3]|max_length[50]',
        'status'   => 'required|in_list[0,1]',  
    ];

    public static $EditAuthorMessages = [
        'newValue' => [
            'required'    => 'La valeur est requise.',
            'min_length'  => 'La valeur doit contenir au moins 3 caractères.',
            'max_length'  => 'La valeur ne doit pas dépasser 50 caractères.',
        ],
        'status' => [
            'required'    => 'Le statut est requis.',
            'in_list'     => 'Le statut doit être 0 ou 1.',
        ]
    ];
}
