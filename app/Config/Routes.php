<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('home', 'HomeController::index');

$routes->get('user', 'UserController::index');
$routes->get('user/(:num)', 'UserController::profile/$1');
//$routes->post('user/update', 'UserController::updateUser');
$routes->get('user/(:num)/subscriptions', 'UserController::subscriptions/$1');


$routes->get('register', 'RegisterController::register');
$routes->post('register', 'RegisterController::register');

//$routes->get('login', 'LoginController::login');
//$routes->post('login', 'LoginController::login'); 
//$routes->get('logout', 'LoginController::logout'); 

$routes->get('auth', 'AuthController::login');
$routes->post('auth', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->post('logout', 'AuthController::logout');

// $routes->get('user/edit/(:num)', 'UserController::editProfile/$1');
// $routes->post('user/edit/(:num)', 'UserController::editProfile/$1');
$routes->get('user/update/(:num)', 'UserController::updateProfile/$1');
$routes->post('user/update/(:num)', 'UserController::updateProfile/$1');

$routes->get('dashboard', 'DashboardController::index');
$routes->post('dashboard/roles/update', 'RoleController::updateRole');
$routes->post('dashboard/roles/add', 'RoleController::addRole');
$routes->post('dashboard/publishers/update', 'PublisherController::updatePublisher');
$routes->post('dashboard/publishers/add', 'PublisherController::addPublisher');
$routes->post('dashboard/languages/update', 'LanguageController::updateLanguage');
$routes->post('dashboard/languages/add', 'LanguageController::addLanguage');
$routes->post('dashboard/genres/update', 'GenreController::updateGenre');
$routes->post('dashboard/genres/add', 'GenreController::addGenre');
$routes->post('dashboard/subgenres/update', 'SubgenreController::updateSubgenre');
$routes->post('dashboard/subgenres/add', 'SubGenreController::addSubgenre');
$routes->post('dashboard/subgenres/associate', 'SubGenreController::associateSubgenreToGenre');
$routes->post('dashboard/authors/update', 'AuthorController::updateAuthor'); 
$routes->post('dashboard/authors/add', 'AuthorController::addAuthor');
$routes->post('dashboard/series/update', 'SerieController::updateSerie'); 
$routes->post('dashboard/series/add', 'SerieController::addSerie');

$routes->get('series', 'SerieController::index');
$routes->get('authors', 'AuthorController::index');

$routes->get('books', 'BookController::index');
$routes->post('books/add', 'BookController::addBook');
$routes->get('book/edit/(:num)', 'BookController::editBook/$1');
$routes->post('book/updateBook', 'BookController::updateBook');
$routes->post('getAuthorBooks', 'BookController::getAuthorBooks');
$routes->get('book/details/(:num)', 'BookController::getBookDetails/$1');
$routes->post('book/deactivate/(:num)', 'BookController::deactivateBook/$1');
$routes->post('serie/details/(:num)', 'SerieController::getSerieDetails/$1');

$routes->post('book/subscribe/(:num)', 'BookController::subscribeBook/$1');

