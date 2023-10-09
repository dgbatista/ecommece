<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\AddressHandler;
use \src\handlers\CartHandler;
use \src\handlers\CategoryHandler;
use \src\handlers\OrderHandler;
use \src\handlers\ProductHandler;
use \src\handlers\UserHandler;
use \src\models\Addresse;
use \src\models\Cart;


class SiteController extends Controller
{

    private $loggedUser = 0;


    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
    }

    public function index()
    {

        $person = 0;
        if ($this->loggedUser != false) {
            $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        $productsIndex = ProductHandler::getProducts();

        $cart = CartHandler::getFullCart();

        $this->render('index', [
            'cart' => $cart,
            'products' => $productsIndex,
            'menuCurrent' => 'home',
            'loggedUser' => $person
        ]);
    }

    public function logout()
    {
        if (!empty($_SESSION['token'])) {
            $_SESSION['token'] = '';
            $this->redirect('/login');
        }
    }

    public function categories($args)
    {
        $idcategory = (int) $args['id'];
        $page = intval(filter_input(INPUT_GET, 'page'));

        $category = CategoryHandler::getCategoryById($idcategory);
        $products = CategoryHandler::getProductsPerPage(true, $idcategory, $page);

        if ($category) {
            $this->render('category', [
                'category' => $category,
                'products' => $products,
                'page' => $page
            ]);
        } else {
            $this->redirect('index');
        }
    }

    public function product($args)
    {
        $desurl = $args['desurl'];

        $product = ProductHandler::getFromURL($desurl);
        $categories = ProductHandler::getCategories($product->idproduct);

        $person = '';
        if ($this->loggedUser) {
            $person = UserHandler::getUserById($this->loggedUser->iduser);
        }

        $cart = CartHandler::getFullCart();

        if ($product) {
            $this->render('detalhes-produto', [
                'product' => $product,
                'categories' => $categories,
                'menuCurrent' => 'products',
                'loggedUser' => $person,
                'cart' => $cart
            ]);
        } else {
            $this->redirect('index');
        }
    }

    public function products()
    {
        echo 'produtos';
    }

    public function checkout()
    {
        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $error = '';
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }
        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $zipcode = filter_input(INPUT_GET, 'zipcode');
        
        $address = AddressHandler::loadAddress($person->idperson);
        
        if(isset($zipcode) && !empty($zipcode)){
            $loadCep = AddressHandler::loadFromCep($zipcode);
            
            if(!$loadCep){
                $_SESSION['error'] = "CEP não encontrado";
                $error = $_SESSION['error'];
            } else {
                $address = $loadCep;
                $error = '';
            }
        }
  
        $cart = CartHandler::getFullCart();

        $this->render('checkout', [
            'address' => $address,
            'error' => isset($_SESSION['error']) ? $_SESSION['error'] : $error,
            'loggedUser' => $person,
            'products' => $cart[1]['carts'],
            'cart' => $cart
        ]);

    }

    public function checkout_order(){

        $desaddress = filter_input(INPUT_POST, 'desaddress');
        $desnumber = filter_input(INPUT_POST, 'desnumber');
        $descomplement = filter_input(INPUT_POST, 'descomplement');
        $desdistrict = filter_input(INPUT_POST, 'desdistrict');
        $descity = filter_input(INPUT_POST, 'descity');
        $desstate = filter_input(INPUT_POST, 'desstate');
        $descountry = filter_input(INPUT_POST, 'descountry');
        $nrzipcode = filter_input(INPUT_POST, 'zipcode');

        if(!isset($nrzipcode) || empty($nrzipcode)){
            $_SESSION['error'] = 'Preencha o CEP';
            $this->redirect('/checkout');
        }
        if(!isset($desaddress) || empty($desaddress)){
            $_SESSION['error'] = 'Preencha o endereço';
            $this->redirect('/checkout');
        }
        if(!isset($desnumber) || empty($desnumber)){
            $_SESSION['error'] = 'Preencha o número';
            $this->redirect('/checkout');
        }
        if(!isset($desdistrict) || empty($desdistrict)){
            $_SESSION['error'] = 'Preencha o bairro';
            $this->redirect('/checkout');
        }
        if(!isset($descity) || empty($descity)){
            $_SESSION['error'] = 'Preencha a cidade';
            $this->redirect('/checkout');
        }
        
        $address = new Addresse();
        $address->nrzipcode = $nrzipcode;
        $address->desaddress = $desaddress;
        $address->desnumber = $desnumber;
        $address->descomplement = $descomplement;
        $address->desdistrict = $desdistrict;
        $address->descity = $descity;
        $address->desstate = $desstate;
        $address->descountry = $descountry;

        $user = UserHandler::getUserById($this->loggedUser->iduser);
        $cart = CartHandler::getFullCart();

        $userAddress =  AddressHandler::getAddressById($user->idperson);
        
        if(!$userAddress){
            AddressHandler::saveAddress($user->idperson, $address);
        } else {
            AddressHandler::updateAddress($user->idperson, $address);
        }        
        
        $userAddress =  AddressHandler::getAddressById($user->idperson);
        $address->idaddress = $userAddress->idaddress;

        $order = OrderHandler::saveOrder($cart, $address);

        if(!$order){
            $_SESSION['error'] = 'Não foi possível continuar pois o carrinho está vazio !';
            $this->redirect('/checkout');
        }

        //zerar carrinho após finalizado a order
        

        $this->redirect('/order/'.$order->idorder);
    }

    public function order($args){

        $idOrder = (int)$args['id'];
        
        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }
        
        $error = '';
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }
        
        $cart = CartHandler::getFullCart();
        $person = UserHandler::getUserById($this->loggedUser->iduser);
        $order = OrderHandler::getJoinsOrderById($idOrder);

        $order = (object)$order[0];

        $cart[0]->order = $order->idorder;
        echo '<pre>';
        print_r($cart);
        echo '</pre>';


        $this->render('payment', [
            'cart' => $cart,
            'loggedUser' => $person,
            'error' => $error,
            'order' => $order
        ]);

    }

    public function boleto($args){

        $idOrder = (int) $args['idorder'];        

        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $order = OrderHandler::getJoinsOrderById($idOrder);
        $order = (object)$order[0];
        
        // DADOS DO BOLETO PARA O SEU CLIENTE
        $dias_de_prazo_para_pagamento = 10;
        $taxa_boleto = 5.00;
        $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
        $valor_cobrado = ProductHandler::formatPrice($order->vltotal); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
        $valor_cobrado = str_replace(",", ".",$valor_cobrado);
        $valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

        $dadosboleto["nosso_numero"] = $order->idorder;  // Nosso numero - REGRA: Máximo de 8 caracteres!
        $dadosboleto["numero_documento"] = $order->idorder;	// Num do pedido ou nosso numero
        $dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
        $dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
        $dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
        $dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

        // DADOS DO SEU CLIENTE
        $dadosboleto["sacado"] = $order->desperson;
        $dadosboleto["endereco1"] = $order->desaddress.','.$order->desnumber. ' - ' . $order->desdistrict;
        $dadosboleto["endereco2"] = $order->descity. ' - ' . $order->desstate. ' - ' . $order->descountry. ' - CEP: ' . $order->nrzipcode;

        // INFORMACOES PARA O CLIENTE
        $dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
        $dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
        $dadosboleto["demonstrativo3"] = "";
        $dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
        $dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
        $dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
        $dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

        // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
        $dadosboleto["quantidade"] = "";
        $dadosboleto["valor_unitario"] = "";
        $dadosboleto["aceite"] = "";		
        $dadosboleto["especie"] = "R$";
        $dadosboleto["especie_doc"] = "";


        // ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


        // DADOS DA SUA CONTA - ITAÚ
        $dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
        $dadosboleto["conta"] = "48781";	// Num da conta, sem digito
        $dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

        // DADOS PERSONALIZADOS - ITAÚ
        $dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

        // SEUS DADOS
        $dadosboleto["identificacao"] = "Hcode Treinamentos";
        $dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
        $dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
        $dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
        $dadosboleto["cedente"] = "HCODE TREINAMENTOS LTDA - ME";

        // NÃO ALTERAR!
        $path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'ecommerce'. DIRECTORY_SEPARATOR . 'public'
        . DIRECTORY_SEPARATOR . 'assets'. DIRECTORY_SEPARATOR . 'site'. DIRECTORY_SEPARATOR . 'boleto'. DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR;

        require_once($path.'funcoes_itau.php');
        require_once($path.'layout_itau.php');
        
    }

    public function login()
    {
        $flash = '';
        $flashLogin = '';
        $registerValues = (isset($_SESSION['registerValues']) ? $_SESSION['registerValues'] : ['desperson' => '', 'desemail' => '', 'nrphone' => '']);
        $_SESSION['registerValues'] = NULL;

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }
        if (isset($_SESSION['flashLogin'])) {
            $flashLogin = $_SESSION['flashLogin'];
            unset($_SESSION['flashLogin']);
        }

        $login = filter_input(INPUT_POST, 'login');
        $password = filter_input(INPUT_POST, 'password');

        if (isset($login)) {
            $login = filter_input(INPUT_POST, 'login');
            $password = filter_input(INPUT_POST, 'password');

            if (!empty($password)) {

                $user = UserHandler::verifyLogin($login, $password);

                if ($user != false && count($user) > 0) {
                    $this->redirect('/checkout');
                } else {
                    $_SESSION['flashLogin'] = "Usuário inexistente ou senha inválida";
                    $flashLogin = $_SESSION['flashLogin'];
                    unset($_SESSION['flashLogin']);
                }
            }
        }

        $cartMerge = CartHandler::getFullCart();

        $this->render('login-site', [
            'flash' => $flash,
            'flashLogin' => $flashLogin,
            'registerValues' => $registerValues,
            'cart' => $cartMerge
        ]);
    }

    public function register()
    {

        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone');
        $password = filter_input(INPUT_POST, 'password');

        $user = UserHandler::saveNewPersonUser([
            'inadmin' => 0,
            'desperson' => $name,
            'desemail' => $email,
            'nrphone' => $phone,
            'despassword' => $password
        ]);

        if (!$user) {
            $flash = $_SESSION['flash'];
            $this->redirect('/login');
        }

        $this->redirect('/checkout');

    }

    public function forgot()
    {

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if (isset($email)) {
            $user = UserHandler::validateEmail($email);
            $_SESSION['user'] = $user;

            $this->redirect('/sent', ['user' => $user]);

        }

        $this->render('forgot', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function sent()
    {

        $this->render('forgot-sent', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function forgot_reset()
    {

        $user = (isset($_SESSION['user']) ? $_SESSION['user'][0] : ['desperson' => '', 'desemail' => '']);

        $password = filter_input(INPUT_POST, 'password');

        if (isset($password) && isset($user)) {

            UserHandler::forgotReset($user['idperson'], $password);

            $this->redirect('/forgot-reset-success');
        }

        $this->render('forgot-reset', [
            'loggedUser' => $this->loggedUser,
            'user' => $user
        ]);

    }

    public function forgot_reset_success()
    {

        $this->render('forgot-reset-success', [
            'loggedUser' => $this->loggedUser
        ]);

    }

    public function profile()
    {

        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }

        $profileMsg = '';
        $profileError = '';
        if (isset($_SESSION['error'])) {
            $profileError = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        if (isset($_SESSION['profileMsg'])) {
            $profileMsg = $_SESSION['profileMsg'];
            $_SESSION['profileMsg'] = NULL;
        }

        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $desperson = filter_input(INPUT_POST, 'desperson');
        $desemail = filter_input(INPUT_POST, 'desemail', FILTER_VALIDATE_EMAIL);
        $nrphone = filter_input(INPUT_POST, 'nrphone');

        if (isset($desperson) || isset($desemail)) {
            if (!empty($desperson) && !empty($desemail)) {
                if ($desemail != $person->desemail) {
                    $emailExists = UserHandler::validateEmail($desemail);

                    if ($emailExists) {
                        $_SESSION['error'] = 'Email já cadastrado.';
                        $this->redirect('/profile');
                    }

                    $person->desemail = $desemail;
                }

                $person->desperson = $desperson;
                $person->nrphone = ($nrphone != '') ? $nrphone : null;

                UserHandler::updateUserPerson($person);

                $_SESSION['profileMsg'] = "Dados alterados com sucesso";

            } else {
                $_SESSION['error'] = 'Campos obrigatórios não podem estar vazio.';
                $profileError = $_SESSION['error'];
                $_SESSION['error'] = NULL;
            }
        }

        $cart = CartHandler::getFullCart();

        $this->render('profile', [
            'loggedUser' => $person,
            'profileMsg' => $profileMsg,
            'profileError' => $profileError,
            'cart' => $cart
        ]);
        
        $this->redirect('/order');

    }

    public function profile_orders(){

        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $cart = CartHandler::getFullCart();
        $person = UserHandler::getUserById($this->loggedUser->iduser);

        $orders = UserHandler::getOrders($user->iduser);
        // $orders = (object) $orders;

        foreach($orders as $order){
            $order['vltotal'] = ProductHandler::formatPrice($order['vltotal']);
            $orderArray []= (object)$order;
        }

        $orders = $orderArray;

        $this->render('profile-orders',[
            'loggedUser' => $person,
            'cart' => $cart,
            'orders' => $orders
        ]);
    }

    public function profile_orders_details($args){

        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }

        $idorder = (int)$args['idorder'];

        $order = (object)OrderHandler::getJoinsOrderById($idorder)[0];
        if(!$order){
            $this->redirect('/profile/orders');
        }

        $cart = CartHandler::getFullCart();

        $person = UserHandler::getUserById($this->loggedUser->iduser);
        $cartProducts = OrderHandler::getJoinsOrderByIdCart($order->idorder);

        foreach($cartProducts as $product){
            $product['vltotal'] = ProductHandler::formatPrice($product['vltotal']);
            $productArray []= (object)$product;
        }


        $this->render('profile-orders-detail', [
            'error' => (isset($error) && !empty($error) != '') ? $error : '',
            'loggedUser' => $person,
            'cart' => $cart,
            'order' => $order,
            'cartProducts' => $productArray
        ]);
    }

    public function password_reset(){

        $user = UserHandler::checkLogin();
        if (!$user) { 
            $this->redirect('/login'); 
        }

        $error = '';
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            $_SESSION['error'] = NULL;
        }
        
        $success = '';
        if (isset($_SESSION['success'])) {
            $success = $_SESSION['success'];
            $_SESSION['success'] = NULL;
        }

        $cart = CartHandler::getFullCart();
        $person = UserHandler::getUserById($user->iduser);

        /*POST*/
        $current_pass = filter_input(INPUT_POST, 'current_pass');
        $new_pass = filter_input(INPUT_POST, 'new_pass');
        $new_pass_confirm = filter_input(INPUT_POST, 'new_pass_confirm');

        if(isset($current_pass) && !empty($current_pass)){
            
            $u = UserHandler::verifyLogin($user->deslogin, $current_pass);

            if(!$u){
                $error = 'Senha atual inválida';
            }else if(!isset($new_pass) || empty($new_pass)){ 
                $error = 'Preencha a nova senha';
            } else if(!isset($new_pass_confirm) || empty($new_pass_confirm)){ 
                $error = 'Preencha a confirmação da nova senha';
            } else if($new_pass != $new_pass_confirm){
                $error = 'As novas senhas não conferem';
            } else if($new_pass === $new_pass_confirm){
                
                $result = UserHandler::updatePass($user->iduser, $new_pass);

                if(!$result){
                    $error = 'Erro ao atualizar';
                } else {
                    $success = 'Password atualizado!';
                    $_SESSION['token'] = NULL;
                }

            } else {
                $error = 'As novas senhas não conferem';
            }           
            // UserHanlder::update('despassword',)
        } else if(isset($current_pass) && empty($current_pass)) {
            $error = 'Preencha a senha atual';
        }

        $this->render('profile-change-password', [
            'loggedUser' => $person,
            'cart' => $cart,
            'error' => (isset($error) || $error != '') ? $error  : '',
            'success' => (isset($success) || $success != '') ? $success  : ''
        ]);
    }
    



}