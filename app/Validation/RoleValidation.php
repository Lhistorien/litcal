<?php

namespace App\Validation;

class RoleValidation
{
    public static $RoleRules = [
        'roleName' => 'required|min_length[5]|max_length[50]|is_unique[role.roleName]',
    ];

    public static $RoleMessages = [
        'roleName' => [
            'required' => 'Le nom du rôle est requis.',
            'min_length' => 'Le nom du rôle doit contenir au moins 5 caractères.',
            'max_length' => 'Le nom du rôle ne doit pas dépasser 50 caractères.',
            'is_unique' => 'Ce nom de rôle est déjà utilisé.',
        ],
    ];
}
