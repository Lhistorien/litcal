<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): string
    {
        $user = session()->get('user');
    
        $data = [
            'meta_title' => 'Accueil',
            'title' => 'Bienvenue sur la page d\'accueil',
            'books' => ['Livre 1', 'Livre 2', 'Livre 3', 'Livre 4', 'Livre 5', 'Livre 6'],
            'user' => $user,
        ];
    
        return view('home', $data);
    }

    public function bookPage() 
    {
        $data = [
            'meta_title' => 'Livre',
            'title' => 'Ceci est une page Livre',
        ];
        return view('bookPage');
    }
}