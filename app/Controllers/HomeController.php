<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\LabelSubscriptionModel;

class HomeController extends BaseController
{
    public function index()
    {
        $bookModel = new BookModel();
        
        // Récupérer les livres récents et à paraître
        $recent = $bookModel->getRecentBooks();
        $upcoming = $bookModel->getUpcomingBooks();
    
        // Enrichir les livres avec la propriété 'labels'
        $data['recentBooks'] = $bookModel->enrichBooksWithLabels($recent);
        $data['upcomingBooks'] = $bookModel->enrichBooksWithLabels($upcoming);
        $recent = $bookModel->getRecentBooks();
        $recent = $bookModel->enrichBooksWithLabels($recent);

    
        $data['meta_title'] = 'Bienvenue sur Litcal !';
        
        // Récupérer les abonnements de l'utilisateur, si connecté
        if (session()->get('is_logged_in')) {
            $labelSubModel = new \App\Models\LabelSubscriptionModel();
            $subscriptions = $labelSubModel->getUserLabelSubscriptions(session()->get('user_id'));
            
            $userSubscriptions = [];
            if (!empty($subscriptions)) {
                foreach ($subscriptions as $sub) {
                    $userSubscriptions[] = $sub->label;
                }
            }
            $data['userSubscriptions'] = $userSubscriptions;
        } else {
            $data['userSubscriptions'] = [];
        }
        
        return view('home', $data);
    }      
}