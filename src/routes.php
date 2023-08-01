<?php

use core\Router;

$router = new Router();

/*SITE*/
$router->get('/', 'HomeController@index');
$router->get('/login', 'UserController@signin');
$router->get('/cadastro', 'UserController@signup');




/*ADMIN*/
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'UserController@admin_signin');
$router->post('/admin/login', 'UserController@signin_action');

$router->get('/admin/users', 'AdminController@users' );

$router->get('/admin/logout', 'AdminController@logout');