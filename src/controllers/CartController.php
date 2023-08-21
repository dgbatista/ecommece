<?php
namespace src\controllers;

use \core\Controller;
use \src\models\Cart;
use \src\models\Product;
use \src\handlers\CartHandler;
use \src\handlers\ProductHandler;

class CartController extends Controller {

    public $cart;

    public function __construct(){
        $this->cart = CartHandler::getFromSession();
    }

    public function index(){

        $cart = CartHandler::getFromSession();
        $products = CartHandler::getProducts();

        $this->render('cart', [
            'menuCurrent' => 'cart',
            'products' => $products['carts'],
            'total' => $products['total'],
            'vltotal' => $products['vltotal']
        ]);

    }

    public function add($args){
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        CartHandler::addProducToCart($product, $cart->idcart);

        $this->redirect('/cart');

    }

    public function minus($args){
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        CartHandler::removeProductToCart($product, $cart->idcart);

        $this->redirect('/cart');
    }

    public function remove($args){
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        CartHandler::removeProductToCart($product, $cart->idcart, true);

        $this->redirect('/cart');
    }



}