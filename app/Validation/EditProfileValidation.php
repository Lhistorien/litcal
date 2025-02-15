<?php

namespace App\Validation;

class EditProfileValidation
{
    public static function EditProfileRules($id = null)
    {
        $rules = [
            'pseudo'      => "required|min_length[4]|max_length[30]|is_unique[user.pseudo,id,{$id}]",
            'email'       => "required|valid_email|is_unique[user.email,id,{$id}]",
            'birthday'    => 'required|valid_date',
            // check_old_password est une règle personnalisée, définie dans CustomRules.php
            'OldPassword' => 'required_with[newPassword]|check_old_password',
            'newPassword' => 'permit_empty|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'pwdControl'  => 'permit_empty|matches[newPassword]',
            'status'      => 'permit_empty|in_list[0,1]', 
            'role'        => 'permit_empty|in_list[User,Moderator,Administrator]', 
        ];
    
        return $rules;
    }

    public static $EditProfileMessages = [
        'pseudo' => [
            'required'   => 'Le pseudo est requis.',
            'min_length' => 'Le pseudo doit contenir au moins 4 caractères.',
            'max_length' => 'Le pseudo ne doit pas dépasser 30 caractères.',
            'is_unique'  => 'Ce pseudonyme n\'est pas disponible.',
        ],
        'email' => [
            'required'    => 'L\'adresse email est requise.',
            'valid_email' => 'Veuillez entrer une adresse email valide.',
            'is_unique'   => 'Cette adresse email est déjà associée à un compte.',
        ],
        'birthday' => [
            'required'   => 'La date de naissance est requise.',
            'valid_date' => 'Veuillez entrer une date valide.',
        ],
        'newPassword' => [
            'min_length'  => 'Le mot de passe doit contenir au moins 8 caractères.',
            'max_length'  => 'Le mot de passe ne doit pas dépasser 255 caractères.',
            'regex_match' => 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.',
        ],
        'pwdControl' => [
            'matches' => 'Le mot de passe n\'a pas été répété correctement.',
        ],
        'OldPassword' => [
            'required_with'        => 'Veuillez renseigner votre ancien mot de passe.',
            'check_old_password'   => 'L\'ancien mot de passe est incorrect.',
        ],
        'role' => [
            'required' => 'Le rôle est requis',
        ],
        'status' => [
            'required' => 'Le statut est requis',
        ],
    ];
}
