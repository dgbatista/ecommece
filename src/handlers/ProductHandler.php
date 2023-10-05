<?php
namespace src\handlers;

use \src\models\Product;
use \src\models\ProductsCategorie;

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
        $product->desphoto = self::checkPhoto($data['idproduct']);

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

    public static function getProductById($idproduct){
        $data = Product::select()->where('idproduct', $idproduct)->one();

        if($data){
            $product = self::productArrayToObject($data);

            return $product;
        }

        return false;
    }

    public static function update(Product $p){

         Product::update([
                'desproduct'=> $p->desproduct,
                'vlprice'=> $p->vlprice,
                'vlwidth'=> $p->vlwidth,
                'vlheight'=> $p->vlheight,
                'vllength'=> $p->vllength,
                'vlweight'=> $p->vlweight,
                'desurl'=> $p->desurl
            ])
            ->where('idproduct', $p->idproduct)
        ->execute();

    }

    public static function delete($idproduct){
        Product::delete()
            ->where('idproduct', $idproduct)
        ->execute();

        $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
        'ecommerce' . DIRECTORY_SEPARATOR . 
        'public' . DIRECTORY_SEPARATOR . 
        'assets'. DIRECTORY_SEPARATOR . 
        'site'. DIRECTORY_SEPARATOR . 
        'img'. DIRECTORY_SEPARATOR . 
        'products' . DIRECTORY_SEPARATOR . 
        $idproduct.'.jpg';

        unlink($dist);
    }

    public static function checkPhoto($idProduct){

        if(file_exists(
                $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
                'ecommerce' . DIRECTORY_SEPARATOR . 
                'public' . DIRECTORY_SEPARATOR . 
                'assets'. DIRECTORY_SEPARATOR . 
                'site'. DIRECTORY_SEPARATOR . 
                'img'. DIRECTORY_SEPARATOR . 
                'products' . DIRECTORY_SEPARATOR . 
                $idProduct.'.jpg'
            )){

            return "/assets/site/img/products/".$idProduct.".jpg";
        } else {
            return "/assets/site/img/products/product.jpg";
        }
    }

    public static function uploadPhoto($file, $idProduct){

        $extension = explode('.', $file['name']);
        $extension = end($extension);

        switch($extension){
            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($file["tmp_name"]);
            break;

            case "gif":
                $image = imagecreatefromgif($file["tmp_name"]);
            break;

            case "png":
                $image = imagecreatefrompng($file["tmp_name"]);
            break;
        }

        $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
        'ecommerce' . DIRECTORY_SEPARATOR . 
        'public' . DIRECTORY_SEPARATOR . 
        'assets'. DIRECTORY_SEPARATOR . 
        'site'. DIRECTORY_SEPARATOR . 
        'img'. DIRECTORY_SEPARATOR . 
        'products' . DIRECTORY_SEPARATOR . 
        $idProduct.'.jpg';

        imagejpeg($image, $dist);

        imagedestroy($image);

        self::checkPhoto($idProduct);
    }

    public static function getFromURL($desurl){

        $data = Product::select()
            ->where('desurl', $desurl)
        ->one();

        if($data){
            $product = self::productArrayToObject($data);
            return $product;
        }
        return false;
    }

    public static function getCategories($idproduct) {

        return ProductsCategorie::select()
                ->join('categories', 'productsCategories.idcategory', '=', 'categories.idcategory')
            ->where('idproduct', $idproduct)
        ->get();
    }

    public static function formatPrice($value){
        $value = str_replace(',', '', $value);
        $value = str_replace ('.', ',', $value);

        return $value;
    }
}