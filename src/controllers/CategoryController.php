<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\CategoryHandler;

class CategoryController extends Controller {

    private $loggedUser;
    private $pageActive = 'categories';

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/admin/login');
        }        
    }

    public function index_admin() {

        $categories = CategoryHandler::getCategories();

       $this->render('admin/categories', [
        'categories' => $categories,
        'pageActive' => $this->pageActive
       ]);

       self::updateFile();
    }

    public function create() {

       $descategory = filter_input(INPUT_POST, 'descategory');

       if(!empty($descategory)){
            CategoryHandler::save($descategory);
            $this->redirect('/admin/categories');
       }
       
       $this->render('admin/categories-create',[
            'pageActive' => $this->pageActive
        ]);

       self::updateFile();
       
    }

    public function update($args) {
        $idcategory = $args['id'];        

        if(!empty($idcategory)){
            $category = CategoryHandler::getCategoryById($idcategory);

            if(!empty($category)){
                $descategory = filter_input(INPUT_POST, 'descategory');
        
                if(!empty($descategory)){
                    CategoryHandler::update($idcategory , $descategory);
                    $this->redirect('/admin/categories');
                }

                $this->render('admin/categories-update' , [
                    'category' => $category,
                    'pageActive' => $this->pageActive
                ]);

            } else {
                $this->redirect('/admin/categories');
            } 
        }

        self::updateFile();
    }

    public function delete($args){
        $id = $args['id'];
        
        if(!empty($id)){
            $category = CategoryHandler::getCategoryById($id);

            if($category){
                CategoryHandler::delete($id);
            }
        }

        $this->redirect('/admin/categories');
        
        self::updateFile();
    }

    public static function updateFile(){
        $categories = CategoryHandler::getCategories();

        $html = [];

        foreach($categories as $row){
            array_push($html, '<li><a href="<?=$base;?>/categories/'.$row->idcategory.'">'.$row->descategory.'</a></li>');
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] 
            . DIRECTORY_SEPARATOR ."ecommerce".DIRECTORY_SEPARATOR 
            . "src". DIRECTORY_SEPARATOR . "views" 
            . DIRECTORY_SEPARATOR . "partials" 
            . DIRECTORY_SEPARATOR ."site". DIRECTORY_SEPARATOR 
            . "categories-menu.php", implode('', $html));
    }

    public function index($args){
        $idcategory = (int)$args['id'];

        $category = CategoryHandler::getCategoryById($idcategory);
        
        if($category){
            $this->render('category',[
                'category' => $category,
                'products' => []
            ]);
        } else {
            $this->redirect('index');
        }      

    }

    public function cat_products($args){
        $idcategory = $args['id'];

        $category = CategoryHandler::getCategoryById($idcategory);

        $productsNotRelated = CategoryHandler::getProducts(false, $idcategory);
        $productsRelated = CategoryHandler::getProducts(true, $idcategory);

        $this->render('admin/categories-products', [
            'category' => $category,
            'productsNotRelated' => CategoryHandler::getProducts(false, $idcategory),
            'productsRelated' => CategoryHandler::getProducts(true, $idcategory),
            'pageActive' => $this->pageActive
        ]);
    }    

    public function add($args){
        $idcategory = $args['idcategory'];
        $idproducts = $args['idproduct'];

        CategoryHandler::addProduct($idcategory, $idproducts);

        $this->redirect('/admin/categories/'.$idcategory.'/products');
    }

    public function remove($args){
        $idcategory = $args['idcategory'];
        $idproducts = $args['idproduct'];


        CategoryHandler::removeProduct($idcategory, $idproducts);
        $this->redirect('/admin/categories/'.$idcategory.'/products');
    }

}