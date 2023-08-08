<?php
namespace src\handlers;

use \src\models\Product;

class ProductHandler {

    private static function productArrayToObject($data){

       
        $product = new Product();
        $product->idproduct = $data['idproduct'];
        $product->desproduct = $data['desproduct'];
        $product->vlprice = $data['vlprice'];
        $product->vlwidth = $data['vlwidth'];
        $product->vlheight = $data['vlheight'];
        $product->vllength = $data['vllength'];
        $product->vlweight = $data['vlweight'];
        $product->desurl = $data['desurl'];

        return $product;
    }

    public static function getProducts(){
        $data = Product::select()->get();
        $products = [];

        if(count($data)> 0){
            foreach($data as $product){
                $products[] = self::productArrayToObject($product);
            }
        }
        return $products;
    }

    public static function save(Product $p){

        Product::insert([
            'desproduct' => $p->desproduct,
            'vlprice' => $p->vlprice,
            'vlwidth' => $p->vlwidth,
            'vlheight' =>$p->vlheight,
            'vllength' => $p->vllength,
            'vlweight' => $p->vlweight,
            'desurl' => $p->desurl

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

    

}