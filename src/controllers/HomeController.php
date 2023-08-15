<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\ProductHandler;
use \src\handlers\CategoryHandler;

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
}