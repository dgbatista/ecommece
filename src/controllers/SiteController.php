<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\ProductHandler;
use \src\handlers\CategoryHandler;
use \src\handlers\AddressHandler;
use \src\models\User;
use \src\models\Addresse;

class SiteController extends Controller {

    private $loggedUser = 0;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();      
    }

    public function index() {
        $products = ProductHandler::getProducts();

        $person = 0;
        if($this->loggedUser != false){
           $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        $this->render('index', [
            'products' => $products,
            'menuCurrent' => 'home',
            'loggedUser' => $person
        ]);
    }

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/login');
        }
    }

    public function categories($args){
        $idcategory = (int)$args['id'];
        $page = intval(filter_input(INPUT_GET, 'page'));

        $category = CategoryHandler::getCategoryById($idcategory);
        $products = CategoryHandler::getProductsPerPage(true, $idcategory, $page);

        if($category){
            $this->render('category',[
                'category' => $category,
                'products' => $products,
                'page'=> $page
            ]);
        } else {
            $this->redirect('index');
        }      
    }

    public function product($args){
        $desurl = $args['desurl'];

        $product = ProductHandler::getFromURL($desurl);
        $categories = ProductHandler::getCategories($product->idproduct);
        $person = UserHandler::getUserById($this->loggedUser->iduser);

        if($product){
            $this->render('detalhes-produto',[
                'product' => $product,
                'categories' => $categories,
                'menuCurrent' => 'products',
                'loggedUser' => $person
            ]);
        } else {
            $this->redirect('index');
        }   
    }

    public function products(){
        echo 'produtos';
    }

    public function checkout(){

        $user = UserHandler::checkLogin();

        if(!$user){
            $this->redirect('/login');
        }

        $cepError = '';
        if(isset($_SESSION['error'])){
            $cepError = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        $zipcode = filter_input(INPUT_GET, 'zipcode');
        $desnumber = filter_input(INPUT_GET, 'desnumber');
        $descomplement = filter_input(INPUT_GET, 'descomplement');

        $person = UserHandler::getUserById($user->iduser);  

        
        $userAddress = AddressHandler::getAddressById($person->idperson);   
        
        if(isset($zipcode) && !empty($zipcode)){

            $address = AddressHandler::loadFromCep($zipcode);

            if(!$address){
                $_SESSION['error'] = 'CEP não encontrado.';
                $this->redirect('/checkout');
            }

            $address->desnumber = $desnumber;
            
            if($userAddress->idperson != 0){
                //update
                AddressHandler::updateAddress($person->idperson, $address);

                $userAddress = AddressHandler::getAddressById($person->idperson);
                
            } else{
                //save
                AddressHandler::saveAddress($person->idperson, $address);
            }        

            $userAddress = AddressHandler::getAddressById($person->idperson);
        }        
        
        if(isset($userAddress->nrzipcode) && $userAddress->nrzipcode != 0)
        $userAddress->nrzipcode = AddressHandler::formatCepToView($userAddress->nrzipcode);

        if($userAddress->nrzipcode === 0) $userAddress->nrzipcode =  '';

        $userAddress->desnumber = $desnumber;
        $userAddress->descomplement = $descomplement;

        $this->render('checkout', [
            'address' => $userAddress,
            'error' => $cepError,
            'loggedUser' => $person
        ]);

    }

    public function login(){
        $flash = '';
        $flashLogin = '';
        $registerValues = (isset($_SESSION['registerValues']) ? $_SESSION['registerValues'] : ['desperson'=>'', 'desemail'=>'', 'nrphone'=>'']);
        $_SESSION['registerValues'] = NULL;

        if(isset($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        if(isset($_SESSION['flashLogin'])){
            $flashLogin = $_SESSION['flashLogin'];
            unset($_SESSION['flashLogin']);
        }

        $login = filter_input(INPUT_POST, 'login');
        $password = filter_input(INPUT_POST, 'password');

        if(isset($login)){
            $login = filter_input(INPUT_POST, 'login');
            $password = filter_input(INPUT_POST, 'password');            
        
            if(!empty($password)){
                
                $user = UserHandler::verifyLogin($login, $password);

                if($user!= false && count($user) > 0){
                    $this->redirect('/checkout');
                } else {
                    $_SESSION['flashLogin'] = "Usuário inexistente ou senha inválida";
                    $flashLogin = $_SESSION['flashLogin'];
                    unset($_SESSION['flashLogin']);
                }
            }
        }        

        $this->render('login-site',[
            'flash' => $flash,
            'flashLogin' => $flashLogin,
            'registerValues' => $registerValues
        ]);
    }

    public function register(){

        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone');
        $password = filter_input(INPUT_POST, 'password');

        $user = UserHandler::saveNewPersonUser([
            'inadmin'=>0,
            'desperson'=>$name,
            'desemail'=>$email,
            'nrphone'=>$phone,
            'despassword'=>$password
        ]);

        if(!$user){
            $flash = $_SESSION['flash'];
            $this->redirect('/login');
        }

        $this->redirect('/checkout');

    }

    public function forgot(){

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if(isset($email)){
            $user = UserHandler::validateEmail($email);
            $_SESSION['user'] = $user;

            $this->redirect('/sent', ['user'=>$user]);
 
        }
        
        $this->render('forgot', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function sent(){

        $this->render('forgot-sent', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function forgot_reset(){

        $user = (isset($_SESSION['user']) ? $_SESSION['user'][0] : ['desperson'=>'', 'desemail'=>'']);
        
        $password = filter_input(INPUT_POST, 'password');

        if(isset($password) && isset($user)){

            UserHandler::forgotReset($user['idperson'], $password);

            $this->redirect('/forgot-reset-success');
        }

        $this->render('forgot-reset', [
            'loggedUser' => $this->loggedUser,
            'user'=> $user
        ]);

    }

    public function forgot_reset_success(){

        $this->render('forgot-reset-success', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function profile(){
        
        if($this->loggedUser === false){
            $this->redirect('/login');
        }

        $profileMsg = '';
        $profileError = '';
        if(isset($_SESSION['error'])){
            $profileError = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        if(isset($_SESSION['profileMsg'])){
            $profileMsg = $_SESSION['profileMsg'];
            $_SESSION['profileMsg'] = NULL;
        }

        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $desperson = filter_input(INPUT_POST, 'desperson');
        $desemail = filter_input(INPUT_POST, 'desemail', FILTER_VALIDATE_EMAIL);
        $nrphone = filter_input(INPUT_POST, 'nrphone');

        if(isset($desperson) || isset($desemail)){
            if(!empty($desperson) && !empty($desemail))  {
                if($desemail != $person->desemail){
                    $emailExists = UserHandler::validateEmail($desemail);
    
                    if($emailExists){
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

        $this->render('profile', [
            'loggedUser' => $person,
            'profileMsg' => $profileMsg,
            'profileError' => $profileError
        ]);
    }

}