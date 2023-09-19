<?php

use core\Router;

$router = new Router();

/**SITE */
$router->get('/', 'SiteController@index');
$router->get('/login', 'UserController@signin');
$router->get('/cadastro', 'UserController@signup');
$router->get('/categories/{id}', 'SiteController@categories');
$router->get('/products/{desurl}', 'SiteController@product');
$router->get('/products' , 'SiteController@products');
$router->get('/cart/{idproduct}/add', 'CartController@add');
$router->get('/cart/{idproduct}/minus', 'CartController@minus');
$router->get('/cart/{idproduct}/remove', 'CartController@remove');
$router->post('/cart/freight', 'CartController@freight');
$router->get('/cart' , 'CartController@index');
$router->get('/checkout', 'SiteController@checkout');
$router->get('/login', 'SiteController@login');
$router->post('/login', 'SiteController@login');


/**ADMIN */
$router->get('/admin/login', 'UserController@admin_signin');
$router->post('/admin/login', 'UserController@signin_action');

$router->get('/admin/users/{id}/delete', 'UserController@delete');
$router->get('/admin/users/{id}/edit', 'UserController@userEdit');
$router->post('/admin/users/update', 'UserController@update');
$router->get('/admin/users/create', 'UserController@create');
$router->post('/admin/users/create', 'UserController@createAction');
$router->get('/admin/users', 'UserController@index');
$router->get('/logout', 'SiteController@logout');

/*CATEGORIES*/
$router->get('/admin/categories/create','CategoryController@create');
$router->post('/admin/categories/create', 'CategoryController@create');
$router->get('/admin/categories/{idcategory}/products/{idproduct}/remove', 'CategoryController@remove');
$router->get('/admin/categories/{idcategory}/products/{idproduct}/add', 'CategoryController@add');
$router->get('/admin/categories/{id}/products', 'CategoryController@cat_products');
$router->get('/admin/categories/{id}/delete', 'CategoryController@delete');
$router->get('/admin/categories/{id}', 'CategoryController@update');
$router->post('/admin/categories/{id}', 'CategoryController@update');
$router->get('/admin/categories', 'CategoryController@index_admin');

/*PRODUTOS*/
$router->get('/admin/products/{id}/delete', 'ProductController@delete');
$router->get('/admin/products/create', 'ProductController@create');
$router->post('/admin/products/create', 'ProductController@create');
$router->get('/admin/products/{id}', 'ProductController@update');
$router->post('/admin/products/{id}', 'ProductController@update');
$router->get('/admin/products', 'ProductController@index_admin');

$router->get('/admin', 'AdminController@index');

