<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('home', 'HomeController::index');
$routes->get('home/bookpage/', 'home::bookPage');

$routes->get('user', 'UserController::index');
$routes->get('user/(:num)', 'UserController::profile/$1');
//$routes->post('user/update', 'UserController::updateUser');


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
$routes->post('/dashboard/publishers/add', 'PublisherController::addPublisher');
$routes->post('dashboard/languages/update', 'LanguageController::updateLanguage');
$routes->post('dashboard/languages/add', 'LanguageController::addLanguage');
$routes->post('dashboard/genres/update', 'GenreController::updateGenre');
$routes->post('dashboard/genres/add', 'GenreController::addGenre');
$routes->post('dashboard/subgenres/update', 'SubgenreController::updateSubgenre');
$routes->post('dashboard/subgenres/add', 'SubGenreController::addSubgenre');
$routes->post('dashboard/subgenres/associate', 'SubGenreController::associateSubgenreToGenre');

$routes->get('series', 'SerieController::index');