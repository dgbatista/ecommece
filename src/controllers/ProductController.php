<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\ProductHandler;
use \src\models\Product;

class ProductController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/login');
        }        
    }

    public function index_admin() {

        $products = ProductHandler::getProducts();

       $this->render('admin/products', [
        'products' => $products
       ]);
    }

    public function create() {

       $desproduct = filter_input(INPUT_POST, 'desproduct');
       $vlprice = filter_input(INPUT_POST, 'vlprice');
       $vlwidth = filter_input(INPUT_POST, 'vlwidth');
       $vlheight = filter_input(INPUT_POST, 'vlheight');
       $vllength = filter_input(INPUT_POST, 'vllength');
       $vlweight = filter_input(INPUT_POST, 'vlweight');

       if($desproduct  && $vlprice && $vlwidth && $vlheight && $vllength && $vlweight){

            $newProduct = new Product();
            $newProduct->desproduct = $desproduct;
            $newProduct->vlprice = $vlprice;
            $newProduct->vlwidth = $vlwidth;
            $newProduct->vlheight = $vlheight;
            $newProduct->vllength = $vllength;
            $newProduct->vlweight = $vlweight;
            $newProduct->desurl = strtolower(str_replace(' ','-', $desproduct));

            ProductHandler::save($newProduct);
            $this->redirect('/admin/products');
       }       

       $this->render('admin/products-create');       
    }

    public function update($args) {
        $idproduct = $args['id'];        

        if(!empty($idproduct)){
            $product = ProductHandler::getProductById($idproduct);

            if($product){
                $desproduct = filter_input(INPUT_POST, 'desproduct');
        
                if(!empty($desproduct)){
                    ProductHandler::update($idproduct , $desproduct);
                    $this->redirect('/admin/products');
                }

                $this->render('admin/products-update' , [
                    'products' => $product
                ]);

            } else {
                $this->redirect('/admin/products');
            } 
        }
    }

    public function delete($args){
        $id = $args['id'];
        
        if(!empty($id)){
            $category = CategoryHandler::getCategoryById($id);

            if($category){
                ProductHandler::delete($id);
            }
        }

        $this->redirect('/admin/categories');
    }
}