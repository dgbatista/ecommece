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
            'qtd' => $products['qtd_product'],
            'total' => $products['total']
        ]);
    }

    public function add($args){
        $qtd = (int)filter_input(INPUT_GET, 'qtd');
        $qtd = ($qtd != 0) ? (int)filter_input(INPUT_GET, 'qtd') : 1;
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        for($q=0;$q<$qtd; $q++){
            CartHandler::addProducToCart($product, $cart->idcart);     
        }            

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