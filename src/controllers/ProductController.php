<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\ProductHandler;
use \src\models\Product;

class ProductController extends Controller {

    private $loggedUser;
    private $pageActive = 'products';

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/login');
        }        
    }

    public function index_admin() {

        $products = ProductHandler::getProducts();

       $this->render('admin/products', [
        'products' => $products,
        'pageActive' => $this->pageActive
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
       
       $this->render('admin/products-create',[
            'pageActive' => $this->pageActive
       ]);       
    }

    public function update($args) {
        $idproduct = $args['id'];       

        if(!empty($idproduct)){
            $product = ProductHandler::getProductById($idproduct);

            if($product){
                if(!empty($_POST)){
           
                    $product->desproduct = $_POST['desproduct'];
                    $product->vlprice = $_POST['vlprice'];
                    $product->vlwidth = $_POST['vlwidth'];
                    $product->vlheight = $_POST['vlheight'];
                    $product->vllength = $_POST['vllength'];
                    $product->vlweight = $_POST['vlweight'];

                    /*FOTO*/
                    if(isset($_FILES['file']) && !empty($_FILES['file']['tmp_name'])){                        
                         ProductHandler::uploadPhoto($_FILES['file'], $idproduct);                        
                    } 

                    ProductHandler::update($product);
                    $this->redirect('/admin/products');

                } 
                $this->render('admin/products-update' , [
                    'product' => $product, 
                    'pageActive' => $this->pageActive                   
                ]);
            } else {
                $this->redirect('/admin/products');
            } 
        }
    }

    public function delete($args){
        $id = $args['id'];
           
        if(!empty($id) && $this->loggedUser->inadmin === 1){
            $product = ProductHandler::getProductById($id);

            if($product){
                ProductHandler::delete($id);
            }
        }

        $this->redirect('/admin/products');
    }

    
}