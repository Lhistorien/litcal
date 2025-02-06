<?php

namespace App\Validation;

class RegisterValidation 
{
    public static $RegistrationRules = [
        'pseudo' => 'required|min_length[4]|max_length[30]|is_unique[user.pseudo]',
        'email' => 'required|valid_email|is_unique[user.email]',
        'birthday' => 'required|valid_date',
        'password' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
        'pwdcontrol' => 'required|matches[password]'
    ];

    public static $RegistrationMessages = [
        'pseudo' => [
            'required' => 'Le pseudo est requis.',
            'min_length' => 'Le pseudo doit contenir au moins 4 caractères.',
            'max_length' => 'Le pseudo ne doit pas dépasser 30 caractères.',
            'is_unique' => 'Ce pseudonyme n\'est pas disponible',
        ],
        'email' => [
            'required' => 'L\'adresse email est requise.',
            'valid_email' => 'Veuillez entrer une adresse email valide.',
            'is_unique' => 'Cette adresse email est déjà associée à un compte',
        ],
        'birthday' => [
            'required' => 'La date de naissance est requise.',
            'valid_date' => 'Veuillez entrer une date valide.',
        ],
        'password' => [
            'required' => 'Le mot de passe est requis.',
            'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'max_length' => 'Le mot de passe ne doit pas dépasser 255 caractères.',
            'regex_match' => 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.',
        ],
        'pwdcontrol' => [
            'required' => 'Veuillez répéter le mot de passe.',
            'matches' => 'Le mot de passe n\'a pas été répété correctement.',
        ]
    ];
}