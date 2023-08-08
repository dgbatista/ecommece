<?php

use core\Router;

$router = new Router();

/**SITE */
$router->get('/', 'HomeController@index');
$router->get('/login', 'UserController@signin');
$router->get('/cadastro', 'UserController@signup');

$router->get('/categories/{id}', 'CategoryController@index');


/**ADMIN */
$router->get('/admin/login', 'UserController@admin_signin');
$router->post('/admin/login', 'UserController@signin_action');

$router->get('/admin/users/{id}/delete', 'UserController@delete');
$router->get('/admin/users/{id}/edit', 'UserController@userEdit');
$router->post('/admin/users/update', 'UserController@update');
$router->get('/admin/users/create', 'UserController@create');
$router->post('/admin/users/create', 'UserController@createAction');
$router->get('/admin/users', 'UserController@index');
$router->get('/admin', 'AdminController@index');

$router->get('/logout', 'HomeController@logout');

/*CATEGORIES*/
$router->get('/admin/categories/create','CategoryController@create');
$router->post('/admin/categories/create', 'CategoryController@create');

$router->get('/admin/categories/{id}/delete', 'CategoryController@delete');
$router->get('/admin/categories/{id}', 'CategoryController@update');
$router->post('/admin/categories/{id}', 'CategoryController@update');
$router->get('/admin/categories', 'CategoryController@index_admin');

$router->get('/admin/products/{id}', 'ProductController@update');
$router->post('/admin/products/create', 'ProductController@create');
$router->get('/admin/products/create', 'ProductController@create');
$router->get('/admin/products', 'ProductController@index_admin');

