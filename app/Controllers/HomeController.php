<?php

namespace App\Controllers;

use App\Models\BookModel;

class HomeController extends BaseController
{
    public function index()
    {
        $bookModel = new BookModel();
        
        $data['recentBooks'] = $bookModel->getRecentBooks();
        $data['upcomingBooks'] = $bookModel->getUpcomingBooks();
        $data['meta_title'] = 'Bienvenue sur Litcal !';
        
        // Vous pouvez ajouter d'autres données à passer à la vue...
        return view('home', $data);
    }
}
