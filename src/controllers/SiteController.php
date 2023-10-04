<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\AddressHandler;
use \src\handlers\CartHandler;
use \src\handlers\CategoryHandler;
use \src\handlers\OrderHandler;
use \src\handlers\ProductHandler;
use \src\handlers\UserHandler;
use \src\models\Addresse;

class SiteController extends Controller
{

    private $loggedUser = 0;


    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
    }

    public function index()
    {

        $person = 0;
        if ($this->loggedUser != false) {
            $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        $productsIndex = ProductHandler::getProducts();

        $cart = CartHandler::getFullCart();

        $this->render('index', [
            'cart' => $cart,
            'products' => $productsIndex,
            'menuCurrent' => 'home',
            'loggedUser' => $person
        ]);
    }

    public function logout()
    {
        if (!empty($_SESSION['token'])) {
            $_SESSION['token'] = '';
            $this->redirect('/login');
        }
    }

    public function categories($args)
    {
        $idcategory = (int) $args['id'];
        $page = intval(filter_input(INPUT_GET, 'page'));

        $category = CategoryHandler::getCategoryById($idcategory);
        $products = CategoryHandler::getProductsPerPage(true, $idcategory, $page);

        if ($category) {
            $this->render('category', [
                'category' => $category,
                'products' => $products,
                'page' => $page
            ]);
        } else {
            $this->redirect('index');
        }
    }

    public function product($args)
    {
        $desurl = $args['desurl'];

        $product = ProductHandler::getFromURL($desurl);
        $categories = ProductHandler::getCategories($product->idproduct);

        $person = '';
        if ($this->loggedUser) {
            $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        $cart = CartHandler::getFullCart();

        if ($product) {
            $this->render('detalhes-produto', [
                'product' => $product,
                'categories' => $categories,
                'menuCurrent' => 'products',
                'loggedUser' => $person,
                'cart' => $cart
            ]);
        } else {
            $this->redirect('index');
        }
    }

    public function products()
    {
        echo 'produtos';
    }

    public function checkout()
    {
        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $error = '';
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }
        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $zipcode = filter_input(INPUT_GET, 'zipcode');
        
        $address = AddressHandler::loadAddress($person->idperson);
        
        if(isset($zipcode) && !empty($zipcode)){
            $loadCep = AddressHandler::loadFromCep($zipcode);
            
            if(!$loadCep){
                $_SESSION['error'] = "CEP não encontrado";
                $error = $_SESSION['error'];
            } else {
                $address = $loadCep;
                $error = '';
            }
        }
  
        $cart = CartHandler::getFullCart();

        $this->render('checkout', [
            'address' => $address,
            'error' => isset($_SESSION['error']) ? $_SESSION['error'] : $error,
            'loggedUser' => $person,
            'products' => $cart[1]['carts'],
            'cart' => $cart
        ]);

    }

    public function checkout_order(){

        $desaddress = filter_input(INPUT_POST, 'desaddress');
        $desnumber = filter_input(INPUT_POST, 'desnumber');
        $descomplement = filter_input(INPUT_POST, 'descomplement');
        $desdistrict = filter_input(INPUT_POST, 'desdistrict');
        $descity = filter_input(INPUT_POST, 'descity');
        $desstate = filter_input(INPUT_POST, 'desstate');
        $descountry = filter_input(INPUT_POST, 'descountry');
        $nrzipcode = filter_input(INPUT_POST, 'zipcode');

        if(!isset($nrzipcode) || empty($nrzipcode)){
            $_SESSION['error'] = 'Preencha o CEP';
            $this->redirect('/checkout');
        }
        if(!isset($desaddress) || empty($desaddress)){
            $_SESSION['error'] = 'Preencha o endereço';
            $this->redirect('/checkout');
        }
        if(!isset($desnumber) || empty($desnumber)){
            $_SESSION['error'] = 'Preencha o número';
            $this->redirect('/checkout');
        }
        if(!isset($desdistrict) || empty($desdistrict)){
            $_SESSION['error'] = 'Preencha o bairro';
            $this->redirect('/checkout');
        }
        if(!isset($descity) || empty($descity)){
            $_SESSION['error'] = 'Preencha a cidade';
            $this->redirect('/checkout');
        }
        
        $address = new Addresse();
        $address->nrzipcode = $nrzipcode;
        $address->desaddress = $desaddress;
        $address->desnumber = $desnumber;
        $address->descomplement = $descomplement;
        $address->desdistrict = $desdistrict;
        $address->descity = $descity;
        $address->desstate = $desstate;
        $address->descountry = $descountry;

        $user = UserHandler::getUserById($this->loggedUser->iduser);
        $cart = CartHandler::getFullCart();

        $userAddress =  AddressHandler::getAddressById($user->idperson);
        
        if(!$userAddress){
            AddressHandler::saveAddress($user->idperson, $address);
        } else {
            AddressHandler::updateAddress($user->idperson, $address);
        }        
        
        $userAddress =  AddressHandler::getAddressById($user->idperson);
        $address->idaddress = $userAddress->idaddress;

        $order = OrderHandler::saveOrder($cart, $address);

        if(!$order){
            $_SESSION['error'] = 'Não foi possível continuar pois o carrinho está vazio !';
            $this->redirect('/checkout');
        }

        $this->redirect('/order/'.$order->idorder);
    }

    public function order($args){

        $idOrder = (int)$args['id'];

        $order = OrderHandler::getJoinsOrderById($idOrder);

        echo '<pre>';
        print_r($order);
        echo '</pre>';
        exit;

        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $error = '';
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        $cart = CartHandler::getFullCart();
        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $this->render('payment', [
            'cart' => $cart,
            'loggedUser' => $person,
            'error' => $error,
            'order' => $order
        ]);

    }

    public function login()
    {
        $flash = '';
        $flashLogin = '';
        $registerValues = (isset($_SESSION['registerValues']) ? $_SESSION['registerValues'] : ['desperson' => '', 'desemail' => '', 'nrphone' => '']);
        $_SESSION['registerValues'] = NULL;

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        if (isset($_SESSION['flashLogin'])) {
            $flashLogin = $_SESSION['flashLogin'];
            unset($_SESSION['flashLogin']);
        }

        $login = filter_input(INPUT_POST, 'login');
        $password = filter_input(INPUT_POST, 'password');

        if (isset($login)) {
            $login = filter_input(INPUT_POST, 'login');
            $password = filter_input(INPUT_POST, 'password');

            if (!empty($password)) {

                $user = UserHandler::verifyLogin($login, $password);

                if ($user != false && count($user) > 0) {
                    $this->redirect('/checkout');
                } else {
                    $_SESSION['flashLogin'] = "Usuário inexistente ou senha inválida";
                    $flashLogin = $_SESSION['flashLogin'];
                    unset($_SESSION['flashLogin']);
                }
            }
        }

        $cartMerge = CartHandler::getFullCart();

        $this->render('login-site', [
            'flash' => $flash,
            'flashLogin' => $flashLogin,
            'registerValues' => $registerValues,
            'cart' => $cartMerge
        ]);
    }

    public function register()
    {

        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone');
        $password = filter_input(INPUT_POST, 'password');

        $user = UserHandler::saveNewPersonUser([
            'inadmin' => 0,
            'desperson' => $name,
            'desemail' => $email,
            'nrphone' => $phone,
            'despassword' => $password
        ]);

        if (!$user) {
            $flash = $_SESSION['flash'];
            $this->redirect('/login');
        }

        $this->redirect('/checkout');

    }

    public function forgot()
    {

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if (isset($email)) {
            $user = UserHandler::validateEmail($email);
            $_SESSION['user'] = $user;

            $this->redirect('/sent', ['user' => $user]);

        }

        $this->render('forgot', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function sent()
    {

        $this->render('forgot-sent', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function forgot_reset()
    {

        $user = (isset($_SESSION['user']) ? $_SESSION['user'][0] : ['desperson' => '', 'desemail' => '']);

        $password = filter_input(INPUT_POST, 'password');

        if (isset($password) && isset($user)) {

            UserHandler::forgotReset($user['idperson'], $password);

            $this->redirect('/forgot-reset-success');
        }

        $this->render('forgot-reset', [
            'loggedUser' => $this->loggedUser,
            'user' => $user
        ]);

    }

    public function forgot_reset_success()
    {

        $this->render('forgot-reset-success', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function profile()
    {

        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }

        $profileMsg = '';
        $profileError = '';
        if (isset($_SESSION['error'])) {
            $profileError = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        if (isset($_SESSION['profileMsg'])) {
            $profileMsg = $_SESSION['profileMsg'];
            $_SESSION['profileMsg'] = NULL;
        }

        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $desperson = filter_input(INPUT_POST, 'desperson');
        $desemail = filter_input(INPUT_POST, 'desemail', FILTER_VALIDATE_EMAIL);
        $nrphone = filter_input(INPUT_POST, 'nrphone');

        if (isset($desperson) || isset($desemail)) {
            if (!empty($desperson) && !empty($desemail)) {
                if ($desemail != $person->desemail) {
                    $emailExists = UserHandler::validateEmail($desemail);

                    if ($emailExists) {
                        $_SESSION['error'] = 'Email já cadastrado.';
                        $this->redirect('/profile');
                    }

                    $person->desemail = $desemail;
                }

                $person->desperson = $desperson;
                $person->nrphone = ($nrphone != '') ? $nrphone : null;

                UserHandler::updateUserPerson($person);

                $_SESSION['profileMsg'] = "Dados alterados com sucesso";

            } else {
                $_SESSION['error'] = 'Campos obrigatórios não podem estar vazio.';
                $profileError = $_SESSION['error'];
                $_SESSION['error'] = NULL;
            }
        }

        $cart = CartHandler::getFullCart();

        $this->render('profile', [
            'loggedUser' => $person,
            'profileMsg' => $profileMsg,
            'profileError' => $profileError,
            'cart' => $cart
        ]);
        
        $this->redirect('/order');

    }

    



}