<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\CartHandler;
use \src\models\Cart;

class CartController extends Controller {

    public function index(){
        
        $cart = new Cart();

        // CartHandler::save($cart);

        $this->render('cart', [
            'menuCurrent' => 'cart'
        ]);

    }



}