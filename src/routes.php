<?php

use core\Router;

$router = new Router();

/*SITE*/
$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->get('/cadastro', 'LoginController@signup');




/*ADMIN*/
$router->get('/admin', 'AdminController@index');
$router->get('/admin/login', 'LoginController@admin_signin');
$router->post('/admin/login', 'LoginController@signin_action');

$router->get('/admin/logout', 'LoginController@logout');