<?php
namespace src\handlers;

use \src\models\Cart;
use \src\models\User;
use \src\models\CartsProduct;
use \src\models\Product;
use ClanCats\Hydrahon\Query\Sql\Func;
use \src\handlers\UserHandler;

class CartHandler {

    const SESSION = "Cart";

    private static function transformCartToObject($array){

        $cart = new Cart();

        if(!empty($array['idcart'])){
            $cart->idcart = $array['idcart'];       
        }
        $cart->dessessionid = $array['dessessionid'] ?? '';
        $cart->iduser = $array['iduser'] ?? null;
        $cart->deszipcode = $array['deszipcode'] ?? null;
        $cart->vlfreight = $array['vlfreight'] ?? null;
        $cart->nrdays = $array['nrdays'] ?? null;

        return $cart;
    }

    public static function getFromSession(){

        $cart = new Cart();

        $cart = self::getFromSessionID(session_id());

        $user = UserHandler::checkLogin();

        if($user){
            $cart->iduser = $user->iduser;                   
        }

        if(!$cart){

            $data['dessessionid'] = session_id();                
            
            if($user){
                $data['iduser'] = $user->iduser;                   
            }

            $cart = self::transformCartToObject($data);  
            
            $idsession = self::save($cart);
            
            $cart = self::getFromSessionID($data['dessessionid']);

        }

        self::setToSession($cart);

        return $cart;
    }

    public static function setToSession(Cart $cart){
    
        $_SESSION['cart'] = $cart;

    }

    public static function get($idcart){
        $data = Cart::select()->where('idcart', $idcart)->one();

        if($data){
            $cart = self::transformToObject($data);
            return $cart;
        }       
        return false;
    }

    public static function getFromSessionID($idsession = ''){
        $data = Cart::select()->where('dessessionid', $idsession)->one();

        if($data){            
            $cart = self::transformCartToObject($data);
            return $cart;
        }
        
        return false;
    }

    public static function save(Cart $cart){
        Cart::insert([
            'dessessionid'=>$cart->dessessionid,
            'iduser'=>$cart->iduser,
            'deszipcode'=>$cart->deszipcode,
            'vlfreight'=>$cart->vlfreight,
            'nrdays'=>$cart->nrdays
        ])->execute();
    }

    public static function update(Cart $cart){
        Cart::update([
            'iduser'=>$cart->iduser ?? NULL,
            'deszipcode'=>$cart->deszipcode ?? NULL,
            'vlfreight'=>$cart->vlfreight ?? NULL,
            'nrdays'=>$cart->nrdays ?? NULL
            ])
            ->where('idcart', $cart->idcart)
        ->execute();
    }

    public static function addProducToCart(Product $product, $idcart){

        CartsProduct::insert([
            'idcart' => $idcart,
            'idproduct' => $product->idproduct
        ])->execute();

    }

    public static function removeProductToCart(Product $product, $idcart ,$all = false){
        
        if($all){
                CartsProduct::update([
                    'dtremoved'=> date('Y-m-d H:i:s')
                ])
                    ->where('idcart', $idcart)
                    ->where('idproduct', $product->idproduct)
                    ->whereNull('dtremoved')
                ->execute();
        } else {
                CartsProduct::update([
                    'dtremoved'=> date('Y-m-d H:i:s')
                ])
                    ->where('idcart', $idcart)
                    ->where('idproduct', $product->idproduct)
                    ->whereNull('dtremoved')
                    ->limit(1)
                ->execute();
        }
    }

    public static function removeAllProductsToCart($idcart){
        CartsProduct::update([
            'dtremoved'=> date('Y-m-d H:i:s')
        ])
            ->where('idcart', $idcart)
        ->execute();        
    }

    public static function getProducts(){

        $cart = $_SESSION['cart'];

        if($cart){
            $data = CartsProduct::select()
                ->join('products', 'cartsproducts.idproduct', '=', 'products.idproduct')
                ->where('idcart', $cart->idcart)
                ->whereNull('dtremoved')
                ->groupBy('products.idproduct')
            ->get();

            $freight = CartsProduct::select()
                ->join('products as p', 'cartsproducts.idproduct', '=', 'p.idproduct')
                ->where('idcart', $cart->idcart)
                ->whereNull('dtremoved')
                ->addField(new Func('sum', 'p.vlprice'), 'total')
                ->addField(new Func('sum', 'p.vlwidth'), 'vlwidth')
                ->addField(new Func('sum', 'p.vlheight'), 'vlheight')
                ->addField(new Func('sum', 'p.vllength'), 'vllength')
                ->addField(new Func('sum', 'p.vlweight'), 'vlweight')
                ->addField(new Func('count', 'p.idproduct'), 'qtd_products')
            ->get();

            $total = CartsProduct::select()
                ->join('products', 'cartsproducts.idproduct', '=', 'products.idproduct')
                ->where('idcart', $cart->idcart)
                ->whereNull('dtremoved')
                ->groupBy('products.idproduct')
            ->count();  

            $cart = self::arrayToCartObject($data);

            if(!empty($cart)){
                foreach($cart['carts'] as $item){
                    $qtd = self::getProductsById($item->idcart, $item->idproduct);
                    $item->total = $qtd * $item->vlprice;
                    $cart['qtd_product']["$item->idproduct"] = $qtd;
                }
            }
            $freight['vlfreight'] = 10;

            $array['carts'] = $cart['carts'] ?? [];
            $array['total'] = $total;
            $array['qtd_product'] = $cart['qtd_product'] ?? [];
            $array['freight'] = $freight[0] ?? [];

            return $array;
        }
    }

    public static function getProductsById($idcart, $idproduct){

        $data = CartsProduct::select()
            ->where('idcart', $idcart)
            ->where('idproduct', $idproduct)
            ->whereNull('dtremoved')
        ->count();        

        return $data;
    }

    public static function arrayToCartObject($arrays){

        $carts = [];
        $sumProducts = 0;

        if(count($arrays)>0){
            foreach($arrays as $array){
                $cart = new CartsProduct();
                $cart->idcartproduct = $array['idcartproduct'] ?? 0;
                $cart->idcart = $array['idcart'] ?? 0;
                $cart->idproduct = $array['idproduct'] ?? 0;
                $cart->dtremoved = $array['dtremoved'] ?? NULL;
                $cart->desproduct = $array['desproduct'] ?? '';
                $cart->vlprice = $array['vlprice'] ?? 0;
                $cart->vlwidth = $array['vlwidth'] ?? 0;
                $cart->vlheight = $array['vlheight'] ?? 0;
                $cart->vllength = $array['vllength'] ?? 0;
                $cart->vlweight = $array['vlweight'] ?? 0;
                $cart->desurl = $array['desurl']?? '';

                $sumProducts += $cart->vlprice;

                $carts['carts'][] = $cart;
            }
            $carts['sumProduct'] = $sumProducts;
        }       
        return $carts;
    }


    /*MIN - MAX
    Comprimento (C): < 105 && > 16 cm
    Largura (L): < 105 && > 11 cm
    Altura (A): < 105 && > 2 cm
    Diametro (D): < 91 && > 5 cm
    Soma das dimensões < 200 cm */
    public static function calcFreight($zipcode, $zipcodeInformation = []){

        // echo '<pre>';
        // print_r($zipcodeInformation);

        $totals = $zipcodeInformation['qtd_products'];

        if($zipcodeInformation['vlheight'] < 2) $zipcodeInformation['vlheight'] = 2; //altura
        if($zipcodeInformation['vlheight'] > 105) $zipcodeInformation['vlheight'] = 105;
        if($zipcodeInformation['vlwidth'] < 11) $zipcodeInformation['vlwidth'] = 11; //largura
        if($zipcodeInformation['vlwidth'] > 105) $zipcodeInformation['vlwidth'] = 105;
        if($zipcodeInformation['vllength'] > 105) $zipcodeInformation['vllength'] = 105; //comprimento
        if($zipcodeInformation['vllength'] < 16) $zipcodeInformation['vllength'] = 16;
        if($zipcodeInformation['vlweight'] > 1) $zipcodeInformation['vlweight'] = 1;
        if($zipcodeInformation['vlprice'] < 50) $zipcodeInformation['vlprice'] = 50;


        if($totals > 0){

            $qs = http_build_query([
                'nCdEmpresa'=>'',
                'sDsSenha'=>'',
                'nCdServico'=>'40010',
                'sCepOrigem'=>'11730000',
                'sCepDestino'=>$zipcode,
                'nVlPeso'=>$zipcodeInformation['vlweight'],
                'nCdFormato'=>1,
                'nVlComprimento'=>$zipcodeInformation['vllength'],
                'nVlAltura'=>$zipcodeInformation['vlheight'],
                'nVlLargura'=>$zipcodeInformation['vlwidth'],
                'nVlDiametro'=>0,
                'sCdMaoPropria'=>'S',
                'nVlValorDeclarado'=>$zipcodeInformation['vlprice'],
                'sCdAvisoRecebimento'=>'S',
            ]);

            $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);

            $result = $xml->Servicos->cServico;

            return $result;

        } else {

        }

    }

    public static function getFullCart($idcart = false){
        if(!$idcart){
            $cart = CartHandler::getFromSession();
            $productsCart = CartHandler::getProducts();

            $cartMerge[] =$cart;
            $cartMerge[] =$productsCart;
        } else {
            $cart = CartHandler::getFromSession();
            $productsCart = CartHandler::getProducts();

            $cartMerge[] =$cart;
            $cartMerge[] =$productsCart;
        }

        return $cartMerge;
    }

    public static function createNewCart(){
        
    }

}