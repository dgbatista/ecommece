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

    // private $loggedUser;

    // public function __construct() {
    //     $this->loggedUser = UserHandler::checkLogin();
    //     if(UserHandler::checkLogin() === false){
    //         $this->redirect('/login');
    //     }        
    // }

    public function index() {
        $products = ProductHandler::getProducts();

        $this->render('index', [
            'products' => $products,
            'menuCurrent' => 'home'
        ]);
    }

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/admin/login');
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

        if($product){
            $this->render('detalhes-produto',[
                'product' => $product,
                'categories' => $categories,
                'menuCurrent' => 'products'
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

        $person = UserHandler::getUserById($user->iduser);
        
        $address = AddressHandler::getAddressById($person->idperson);

        if(!$address){
            $address = new Addresse();
        }

        $this->render('checkout', [
            'address' => $address,
            'error' => ''
        ]);

    }

    public function login(){

        $login = filter_input(INPUT_POST, 'login');
        $password = filter_input(INPUT_POST, 'password');
        $error = '';        

        if(isset($login)){
            $login = filter_input(INPUT_POST, 'login');
            $password = filter_input(INPUT_POST, 'password');            
        
            if(!empty($password)){
                try{
                    $user = UserHandler::verifyLogin($login, $password);

                    if(count($user) > 0){
                        $this->redirect('/checkout');
                    }
        
                }catch(Exception $e){
                    $error = throw new \Exception("Usuário inexistente ou senha inválida");
                }
            }else{
                echo 'não entrou';
            } 
        }        

        $this->render('login-site', [
            'error' => $error
        ]);
    }
}