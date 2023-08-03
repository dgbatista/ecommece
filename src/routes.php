<?php

use core\Router;

$router = new Router();

/*SITE*/
$router->get('/', 'HomeController@index');
$router->get('/login', 'UserController@signin');
$router->get('/cadastro', 'UserController@signup');

$router->get('/admin/login', 'UserController@admin_signin');
$router->post('/admin/login', 'UserController@signin_action');

$router->get('/admin/users/{id}/delete', 'UserController@delete');
$router->get('/admin/users/{id}/edit', 'UserController@edit');
$router->get('/admin/users/create', 'UserController@create');
$router->post('/admin/users/create', 'UserController@createAction');
$router->get('/admin/users', 'UserController@users' );
$router->get('/admin', 'AdminController@index');

$router->get('/logout', 'HomeController@logout');

