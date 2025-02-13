<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PublisherModel;
use App\Models\LanguageModel;
use App\Models\RoleModel;
use App\Models\GenreModel;
use App\Models\SubGenreModel;
use App\Models\LabelModel;
use App\Models\AuthorModel;
use App\Models\SerieModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_logged_in')) 
        {
            return redirect()->to('/auth')->with('errors', 'Vous devez être connecté.');
        }

        $allowedRoles = ['Administrator', 'Contributor'];
        if (!in_array(session()->get('user_role'), $allowedRoles)) {
            return redirect()->to('/')->with('errors', 'Accès refusé.');
        }

        $userModel = new UserModel();
        $publisherModel = new PublisherModel();
        $languageModel = new LanguageModel();
        $roleModel = new RoleModel();
        $genreModel = new GenreModel();
        $subGenreModel = new SubGenreModel();
        $labelModel = new LabelModel();
        $authorModel = new AuthorModel();
        $serieModel = new SerieModel();

        $users = $userModel->findAll();
        $publishers = $publisherModel->findAll();
        $languages = $languageModel->findAll();
        $roles = $roleModel->findAll();
        $genres = $genreModel->findAll();
        $subgenres = $subGenreModel->getSubgenresWithGenres();
        $labels = $labelModel->findAll();
        $authors = $authorModel->findAll();
        $series = $serieModel->findAll();

        $data = [
            'meta_title' => 'Dashboard',
            'title' => 'Dashboard',
            'users' => $users,
            'publishers' => $publishers,
            'languages' => $languages,
            'roles' => $roles,
            'genres' => $genres,
            'subgenres' => $subgenres,
            'labels' => $labels,
            'authors' => $authors,
            'series' => $series,
        ];

        return view('dashboard', $data);
    }
}