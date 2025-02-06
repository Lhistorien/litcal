<?php

namespace App\Validation;

class EditRoleValidation
{
    public static function EditRoleRules()  
    {
        return 
        [
            'newRoleName' => "required|min_length[3]|max_length[50]|is_unique[role.roleName]",  
        ];
    }

    public static $EditRoleMessages = 
    [
        'newRoleName' =>  
        [
            'required'    => 'Le nom du rôle est requis.',
            'min_length'  => 'Le nom du rôle doit contenir au moins 3 caractères.',
            'max_length'  => 'Le nom du rôle ne doit pas dépasser 50 caractères.',
            'is_unique'   => 'Ce nom de rôle existe déjà.',
        ]
    ];
}
