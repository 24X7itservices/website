<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');


$routes->group('api/customer', ['namespace' => 'App\Controllers\CustomerPanel'], function($routes) {
    
    $routes->post('login', 'Auth::login', ['filter' => 'cors']); 
    $routes->post('encrypt', 'Auth::encode'); 
    $routes->post('decrypt', 'Auth::decode');
    $routes->post('contactFormsubmit', 'Api::contactformsubmit'); 
    $routes->post('addCustomer', 'Api::addCustomer'); 
    $routes->get('jobs', 'Api::getJobopening');
    $routes->post('quotation_request', 'Api::quotationRequestsubmit'); 
    

    $routes->group('', ['filter' => 'jwt'], function($routes) {

        $routes->post('profile', 'Api::getProfile');
        
    });
});