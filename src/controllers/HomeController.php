<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\ProductHandler;

class HomeController extends Controller {

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
            'products' => $products
        ]);
    }

    public function logout(){
        if(!empty($_SESSION['token'])){
            $_SESSION['token'] = '';
            $this->redirect('/admin/login');
        }
    }

    public function test() {

        $this->render('test', [
        ]);
    }
}