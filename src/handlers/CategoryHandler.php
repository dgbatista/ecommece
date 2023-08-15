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

            return Product::select()
                ->join('productscategories', 'products.idproduct', '=' , 'productscategories.idproduct')
                ->where('idcategory', $idcategory)
            ->get();

        } else { 

            return Product::select()
            ->get();
        }
    }

    public static function getProductsPerPage($related = true, $idcategory, $page){

        $perPage = 2;

        if($related){

            $products = Product::select()
                ->join('productscategories', 'products.idproduct', '=' , 'productscategories.idproduct')
                ->where('idcategory', $idcategory)
                ->page($page, $perPage)
            ->get();

            $total = Product::select()
                ->join('productscategories', 'products.idproduct', '=' , 'productscategories.idproduct')
                ->where('idcategory', $idcategory)
            ->count();
            $pageCount = ceil($total / $perPage);

            return [
                'products'=> $products,
                'pageCount' => $pageCount
            ];

        }
    }

    /*Vincula um produto a uma categoria*/
    public static function addProduct($idcategory, $idproduct){

            $product = ProductsCategorie::select()
                ->where('idcategory', $idcategory)
                ->where('idproduct', $idproduct)
            ->one();

            if($product){
                ProductsCategorie::update([
                    'idcategory' => $idcategory,
                    'idproduct' => $idproduct
                    ]) 
                    ->where('idcategory', $product['idcategory'])
                    ->where('idproduct', $product['idproduct'])
                ->execute();
                
            } else {
                ProductsCategorie::insert([
                    'idcategory' => $idcategory,
                    'idproduct' => $idproduct
                    ]) 
                ->execute();
            }
    }       

    public static function removeProduct($idcategory, $idproduct){
                
        ProductsCategorie::delete()
            ->where('idcategory', $idcategory)
            ->where('idproduct', $idproduct)
        ->execute();
    }

    public static function listProduct($list){

        $return = [];

        foreach($list as $item){
            $array = [];
            $array['idproduct'] = $item['idproduct'];
            $array['desproduct'] = $item['desproduct'];

            $return[] = $array;
        }       

        return $array;
    }
    

}