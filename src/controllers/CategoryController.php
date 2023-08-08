<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\CategoryHandler;

class CategoryController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() === false){
            $this->redirect('/login');
        }        
    }

    public function index() {

        $categories = CategoryHandler::getCategories();

       $this->render('admin/categories', [
        'categories' => $categories
       ]);
    }

    public function create() {

       $descategory = filter_input(INPUT_POST, 'descategory');

       if(!empty($descategory)){
            CategoryHandler::save($descategory);
            $this->redirect('/admin/categories');
       }
       
       $this->render('admin/categories-create');
       
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

            } else {
                $this->redirect('/admin/categories');
            }

            self::updateFile();

            $this->render('admin/categories-update' , [
                'category' => $category
            ]);
        }
    }

    public function delete($args){
        $id = $args['id'];
        
        if(!empty($id)){
            $category = CategoryHandler::getCategoryById($id);

            if($category){
                CategoryHandler::delete($id);
            }
        }

        self::updateFile();

        $this->redirect('/admin/categories');

    }

    public static function updateFile(){
        $categories = CategoryHandler::getCategories();

        $html = [];

        foreach($categories as $row){
            array_push($html, '<li><a href="<?=$base;?>/category/'.$row->idcategory.'">'.$row->descategory.'</a></li>');
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] 
            . DIRECTORY_SEPARATOR ."ecommerce".DIRECTORY_SEPARATOR 
            . "src". DIRECTORY_SEPARATOR . "views" 
            . DIRECTORY_SEPARATOR . "partials" 
            . DIRECTORY_SEPARATOR ."site". DIRECTORY_SEPARATOR 
            . "categories-menu.php", implode('', $html));
    }

}