<?php
namespace src\handlers;

use \src\models\Categorie;
use \src\models\Product;
use \src\models\ProductsCategorie;

class CategoryHandler {

    private static function categoryArrayToObject($category){

        $categorie = new Categorie();
        $categorie->idcategory = $category['idcategory'];
        $categorie->descategory = $category['descategory'];
        $categorie->dtregister = $category['dtregister'];

        return $categorie;
    }

    public static function getCategories(){
        $data = Categorie::select()->get();
        $categories = [];

        if(count($data)> 0){
            foreach($data as $category){
                $categories[] = self::categoryArrayToObject($category);
            }
        }

        return $categories;
    }

    public static function save($descategory){

        Categorie::insert([
            'descategory'=> $descategory
        ])->execute();

        return true;
    }

    public static function getCategoryById($idcatagory){
        $data = Categorie::select()->where('idcategory', $idcatagory)->one();

        if($data){
            $category = self::categoryArrayToObject($data);

            return $category;
        }

        return false;
    }

    public static function update($idcategory , $descategory){
        Categorie::update([
                'descategory'=> $descategory
            ])
            ->where('idcategory', $idcategory)
        ->execute();
    }

    public static function delete($idcategory){
        Categorie::delete()
            ->where('idcategory', $idcategory)
        ->execute();
    }

    public static function getProducts($related = true, $idcategory){

        if($related){
            return ProductsCategorie::select()
                ->join('products', 'productscategories.idproduct', '=' , 'products.idproduct')
                ->where('idcategory', $idcategory)
            ->get();
        } else {
            return ProductsCategorie::select()
                ->join('products', 'productscategories.idproduct', '=' , 'products.idproduct')
                ->whereNotIn('idcategory', [$idcategory])
            ->get();
        }
    }

    /*Vincula um produto a uma categoria*/
    public static function addProduct($idcategory, $idproduct){

        ProductsCategorie::insert([
            'idcategory' => $idcategory,
            'idproduct' => $idproduct
            ]) 
        ->execute();
    }

    public static function removeProduct($idcategory, $idproduct){
        ProductsCategorie::delete()
            ->where('idcategory', $idcategory)
            ->where('idproduct', $idproduct)
        ->execute();
    }

    

}