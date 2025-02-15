<?php

namespace App\Validation;

class EditGenreValidation
{
    public static $EditGenreRules = [
        'genreName' => 'required|min_length[3]|max_length[50]|is_unique[genre.genreName,genre.id,{id}]', 
        'status'   => 'required|in_list[0,1]',  
    ];

    public static $EditGenreMessages = [
        'genreName' => [
            'required'    => 'La valeur est requise.',
            'min_length'  => 'La valeur doit contenir au moins 3 caractères.',
            'max_length'  => 'La valeur ne doit pas dépasser 50 caractères.',
            'is_unique'   => 'Ce nom existe déjà.',  
        ],
        'status' => [
            'required'    => 'Le statut est requis.',
            'in_list'     => 'Le statut doit être 0 ou 1.',
        ]
    ];
}
