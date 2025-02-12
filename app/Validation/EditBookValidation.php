<?php

namespace App\Validation;

class EditBookValidation 
{
    public static $EditBookRules = 
    [
        'title' => 'required|max_length[200]',
        'author.*' => 'required|max_length[100]|is_not_unique[author.id]',
        'actor_name[]' => 'if_exist|required_with[actor_role]|max_length[100]|is_not_unique[author.id]',
        'actor_role[]' => 'if_exist|required_with[actor_name]|max_length[50]|is_not_unique[role.roleName]',
        'publisher' => 'required|max_length[100]|is_not_unique[publisher.id]',
        'publication' => 'required|valid_date',
        'preorder' => 'if_exist',
        'language' => 'required|max_length[50]|is_not_unique[language.abbreviation]',
        'price' => 'regex_match[/^\d+(\,|\.)?\d*$/]',
        'isbn' => 'if_exist|max_length[20]',
        'format' => 'required|max_length[50]|is_not_unique[format.format]',
        'link' => 'if_exist|max_length[300]|valid_url',
        'description' => 'if_exist|max_length[2000]',
        // 'cover' => 'max_size[cover,4096]|is_image[cover]',
        'serie' => 'permit_empty|required_with[volume]|max_length[100]|is_not_unique[serie.id]',
        'volume' => 'permit_empty|required_with[serie]|max_length[3]',
        'genre.*' => 'if_exist|max_length[50]|is_not_unique[genre.id]',
        'subgenre.*' => 'if_exist|max_length[50]|is_not_unique[subgenre.id]',
    ];

    public static $EditBookMessages = 
    [
        'title' => 
        [
            'required' => 'Le titre est obligatoire.',
            'max_length' => 'Le titre ne peut pas dépasser 200 caractères.',
        ],
        'author[]' => 
        [
            'required' => 'Il faut indiquer au moins un auteur.',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères.',
            'is_not_unique' => 'Cet auteur n\'existe pas dans la base de données',
        ],
        'actor_name[]' => 
        [
            'required_with' => 'Vous devez choisir un auteur.',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères.',
            'is_not_unique' => 'Cet auteur n\'existe pas dans la base de données',
        ],
        'actor_role[]' => 
        [
            'required_with' => 'Vous devez choisir un role.',
            'max_length' => 'Le nom ne peut pas dépasser 50 caractères.',
            'is_not_unique' => 'Ce role n\'existe pas dans la base de données',
        ],
        'publisher' => 
        [
            'required' => 'Le nom de l\'éditeur est requis.',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères.',
            'is_not_unique' => 'Cet éditeur n\'existe pas dans la base de données',
        ],
        'publication' => 
        [
            'required' => 'La date de publication est requise.',
            'valid_date' => 'Veuillez entrer une date valide.',
        ],
        'language' => 
        [
            'required' => 'La langue est requise.',
            'max_length' => 'La langue ne peut pas dépasser 50 caractères.',
            'is_not_unique' => 'Cette langue n\'existe pas dans la base de données',
        ],
        'price' => 
        [
            'numeric' => 'Ce champs ne peut contenir que des chiffres et des .',
        ],
        'isbn' => 
        [
            'max_length' => 'Cet ISBN est trop long.',
        ],
        'format' => 
        [
            'required' => 'Le format est requis.',
            'max_length' => 'Le format ne peut pas dépasser 50 caractères.',
            'is_not_unique' => 'Ce format n\'existe pas dans la base de données',
        ],
        'link' => 
        [
            'max_length' => 'L\'URL ne peut pas dépasser 300 caractères.',
            'valid_url' => 'Cette URL n\'est pas valide',
        ],
        'description' => 
        [
            'max_length' => 'Le résumé ne peut pas dépasser 2000 caractères.',
        ],
        // 'cover' =>
        // [
        //     'max_size' => 'Ce fichier est trop volumineux. La taille maximale autorisée est 4MB',
        //     'is_image' => 'Vous ne pouvez uploader que des images',
        // ],
        'serie' => 
        [
            'required_with' => 'Vous devez choisir une série.',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères.',
            'is_not_unique' => 'Cette série n\'existe pas dans la base de données',
        ],
        'tome' => 
        [
            'required_with' => 'Vous devez indiquer le tome (HS si c\'est un hors-série).',
            'max_length' => 'Le nom ne peut pas dépasser 3 caractères.',
        ],
        'genre[]' => 
        [
            'max_length' => 'Le genre ne peut pas dépasser 50 caractères.',
            'is_not_unique' => 'Ce genre n\'existe pas dans la base de données',
        ],
        'subgenre[]' => 
        [
            'max_length' => 'Le sous-genre ne peut pas dépasser 50 caractères.',
            'is_not_unique' => 'Ce sous-genre n\'existe pas dans la base de données',
        ],
    ];
}