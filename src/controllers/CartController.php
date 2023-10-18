<?php
namespace src\controllers;

use \core\Controller;
use \src\models\Cart;
use \src\models\Product;
use \src\handlers\CartHandler;
use \src\handlers\ProductHandler;
use \src\handlers\UserHandler;

class CartController extends Controller {

    public $cart;
    public $flash;
    private $loggedUser;

    public function __construct(){
        $this->cart = CartHandler::getFromSession();
        $this->loggedUser = UserHandler::checkLogin();   
    }

    public function index(){

        $cart = CartHandler::getFromSession();
        $products = CartHandler::getProducts();
        $person = 0;
        if($this->loggedUser != false){
           $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        if(isset($_SESSION['flash'])){ 
            $this->flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $cartMerge[] =$cart;
        $cartMerge[] =$products;

        $this->render('cart', [
            'cart' => $cartMerge,
            'menuCurrent' => 'cart',
            'flash' => $this->flash,
            'loggedUser' => $person
        ]);
    }

    public function add($args){
        $qtd = (int)filter_input(INPUT_GET, 'qtd');
        $qtd = ($qtd != 0) ? (int)filter_input(INPUT_GET, 'qtd') : 1;
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        $cart->cartProducts = [];
        for($q=0;$q<$qtd; $q++){
           CartHandler::addProducToCart($product, $cart->idcart);     
        }      
        
        $this->redirect('/cart');

        self::freight();

    }

    public function minus($args){
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        CartHandler::removeProductToCart($product, $cart->idcart);

        $this->redirect('/cart');

        self::freight();
    }

    public function remove($args){
        $idproduct =  $args['idproduct'];
        $cart = CartHandler::getFromSession();
        $product = ProductHandler::getProductById($idproduct);

        CartHandler::removeProductToCart($product, $cart->idcart, true);

        $this->redirect('/cart');

        self::freight();
    }

    public function freight(){

        $zipcode = filter_input(INPUT_POST, 'zipcode');
        $zipcode = str_replace('-', '', $zipcode);
        $_SESSION['zipcode'] = $zipcode;

        if(!empty($_SESSION['zipcode']) && !empty($zipcode)){
            $zipcode = $_SESSION['zipcode'];
        } 

        $cart = CartHandler::getFromSession();
        $products = CartHandler::getProducts();

        if(empty($products['carts'])){
            $cart->vlfreight = null;
            $cart->nrdays = null;
            CartHandler::update($cart);         
        }

        if(!empty($zipcode) && (count($products['carts']) > 0)){
            $result = CartHandler::calcFreight($zipcode, $products['freight']);
            
            if(isset($result) && $result->Erro != 0){
                $_SESSION['flash'] = (string)$result->MsgErro;   
                $this->redirect('/cart');
            } else {
                $cart->deszipcode = $zipcode;
                $cart->vlfreight = number_format(self::formatToDecimal($result->Valor),2);
                $cart->nrdays = (int)$result->PrazoEntrega;

                CartHandler::update($cart);
            }         

            $_SESSION['vlfreight'] = (string)$result->Valor;
            $_SESSION['deadline'] = (string)$result->PrazoEntrega;

            $this->redirect('/cart');

        } else {

            if(count($products['carts']) == 0){
                $_SESSION['flash'] = 'Não é possível calcular o frete de um carrinho vazio.';
            }
            if(empty($zipcode)){
                $_SESSION['flash'] = 'CEP inexistente ou vazio.';
            }
            $this->redirect('/cart');
        }    

    }

    public static function formatToDecimal($value){

        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }

}