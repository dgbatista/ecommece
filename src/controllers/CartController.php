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

    /*MIN - MAX
    Comprimento (C): 15 cm – 100 cm
    Largura (L): 10 cm – 100 cm
    Altura (A): 1 cm – 100 cm
    Soma das dimensões (C+L+A): 25 cm – 200 cm */

    public function freight(){

        $zipcode = filter_input(INPUT_POST, 'zipcode');

        $zipcode = str_replace('-', '', $zipcode);

        $cart = CartHandler::getFromSession();
        $products = CartHandler::getProducts();
        
        CartHandler::calcFreight($zipcode, $products['freight']);        

    }


}