<?php

use CodeIgniter\Router\RouteCollection;
use App\Config\Auth as AuthConfig;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ProfileController::index');

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('accounts', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'AccountsController::index', ['as' => 'accounts']);
    $routes->post('create', 'AccountsController::create');
    $routes->get('create', 'AccountsController::create');
    $routes->post('getroles', 'AccountsController::getRoles');
    $routes->get('index_dev', 'AccountsController::index_dev');

    $routes->get('companies/(:num)', 'AccountsController::companyList/$1');
    $routes->post('getallcompanies', 'AccountsController::getAllCompanies');
    $routes->post('companies/delete', 'AccountsController::deleteCompany');

    $routes->post('companies/getcompanies', 'AccountsController::getCompanies');
    $routes->post('companies/addcompany', 'AccountsController::addCompany');

    $routes->post('getcountries', 'AccountsController::getCountries');
    $routes->post('getstates', 'AccountsController::getStates');
    $routes->post('getcities', 'AccountsController::getCities');
});

$routes->group('profile', ['filter' => 'login'], function ($routes) {
    $routes->get('(:num)', 'ProfileController::index/$1');
    $routes->get('', 'ProfileController::index');
    $routes->get('getprofile', 'ProfileController::getProfile');
    $routes->get('getprofile/(:num)', 'ProfileController::getProfile/$1');
    $routes->post('update', 'ProfileController::updateProfile');
    $routes->post('gettimezones', 'ProfileController::getTimezones');

    $routes->post('getcountries', 'ProfileController::getCountries');
    $routes->post('getcities', 'ProfileController::getCities');
});

$routes->group('companies', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'CompanyController::index');
    $routes->get('create', 'CompanyController::add');
    $routes->post('create', 'CompanyController::add');
    $routes->get('get/(:num)', 'CompanyController::getCompany/$1');
    $routes->post('delete', 'CompanyController::delete');
    $routes->get('update/(:num)', 'CompanyController::update/$1');
    $routes->post('getall', 'CompanyController::getAll');
    $routes->post('getone', 'CompanyController::getOne');

    $routes->post('getcountries', 'CompanyController::getCountries');
    $routes->post('getcities', 'CompanyController::getCities');
    $routes->post('getstates', 'CompanyController::getStates');
    $routes->post('getcompanies', 'CompanyController::getCompanies');

    $routes->get('vessellist/(:num)', 'CompanyController::vesselList/$1');
    $routes->post('getvessels', 'CompanyController::getVessels');

    $routes->get('getuserscompany/(:num)', 'CompanyController::getUsersCompany/$1');
    $routes->post('getuserscompany', 'CompanyController::getUsersCompany');
    $routes->post('deleteusercompany', 'CompanyController::removeUserCompany');

    $routes->post('userlist', 'CompanyController::getUserList');
    $routes->post('addusercompany', 'CompanyController::addUserCompany');
});

$routes->group('settings', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'Settings::index');
    $routes->post('findone', 'Settings::findOne');
    $routes->post('findall', 'Settings::findAll');
    $routes->post('add', 'Settings::add');
    $routes->post('update', 'Settings::update');
    $routes->post('delete', 'Settings::delete');
});

$routes->group('permissions', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'PermissionsController::index');
    $routes->post('getall', 'PermissionsController::getAll');
    $routes->post('getone', 'PermissionsController::getOne');
    $routes->post('add', 'PermissionsController::add');
    $routes->post('update', 'PermissionsController::update');
    $routes->post('delete', 'PermissionsController::delete');
});

$routes->group('groups', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'GroupsController::index');
    $routes->post('getall', 'GroupsController::getAll');
    $routes->post('getone', 'GroupsController::getOne');
    $routes->post('add', 'GroupsController::add');
    $routes->post('update', 'GroupsController::update');
    $routes->post('delete', 'GroupsController::delete');
    $routes->post('getpermissions', 'GroupsController::getPermissions');
});

$routes->group('player', ['filter' => 'login'], function ($routes) {
    $routes->get('', 'PlayerController::index');
    $routes->post('getall', 'PlayerController::getAll');
});

$routes->group('', ['namespace' => 'App\Controllers'], static function ($routes) {
    // Load the reserved routes from Auth.php
    $config = config(AuthConfig::class);

    $reservedRoutes = $config->reservedRoutes;

    // Login/out
    $routes->get($reservedRoutes['login'], 'AuthController::login', ['as' => $reservedRoutes['login']]);
    $routes->post($reservedRoutes['login'], 'AuthController::attemptLogin');
    $routes->get($reservedRoutes['logout'], 'AuthController::logout');

    // Registration
    $routes->get($reservedRoutes['register'], 'AuthController::register', ['as' => $reservedRoutes['register']]);
    $routes->post($reservedRoutes['register'], 'AuthController::attemptRegister');

    // Activation
    $routes->get($reservedRoutes['activate-account'], 'AuthController::activateAccount', ['as' => $reservedRoutes['activate-account']]);
    $routes->get($reservedRoutes['resend-activate-account'], 'AuthController::resendActivateAccount', ['as' => $reservedRoutes['resend-activate-account']]);

    // Forgot/Resets
    $routes->get($reservedRoutes['forgot'], 'AuthController::forgotPassword', ['as' => $reservedRoutes['forgot']]);
    $routes->post($reservedRoutes['forgot'], 'AuthController::attemptForgot');
    $routes->get($reservedRoutes['reset-password'], 'AuthController::resetPassword', ['as' => $reservedRoutes['reset-password']]);
    $routes->post($reservedRoutes['reset-password'], 'AuthController::attemptReset');
});

$routes->group('migrations', ['filter' => 'login'], function ($routes) {
    $routes->get('moveusers/(:num)', 'MigrationTools::moveUsers/$1');
});
