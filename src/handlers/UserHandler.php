<?php
namespace src\handlers;

use \src\models\User;
use \src\models\Order;
use \src\models\Person;

class UserHandler {

    public static function checkLogin() {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0) {

                $loggedUser = new User();
                $loggedUser->iduser = $data['iduser'];
                $loggedUser->deslogin = $data['deslogin'];
                $loggedUser->inadmin = $data['inadmin'];
                $loggedUser->token = $data['token'];

                return $loggedUser;
            }
        }

        return false;
    }
    public static function verifyLogin($login, $password){ 

        $user = User::select()->where('deslogin', $login)->one();
        $dataUser = [];
    
        if($user){

            if(password_verify($password, $user['despassword'])){

                $dataUser['admin'] = $user['inadmin'];

                $dataUser['token'] = md5(time().rand(0,9999).time());

                User::update()
                    ->set('token', $dataUser['token'])
                    ->where('deslogin', $login)
                ->execute();

                $_SESSION['token'] = $dataUser['token'];

                return $dataUser;
            }
        }

        return false;
    }

    public static function getAllUsers(){
        $userList = User::select()
            ->join('persons', 'users.idperson', '=' , 'persons.idperson')
            ->orderBy('desperson')
        ->get();
        $users = [];

        if($userList) {
            foreach($userList as $data){
            
                $user = new User();
                $user->iduser = $data['iduser'];
                $user->idperson = $data['idperson'];
                $user->deslogin = $data['deslogin'];
                $user->inadmin = $data['inadmin'];
                $user->dtregisterty = $data['dtregister'];
                $user->desperson = $data['desperson'];
                $user->desemail = $data['desemail'];
                $user->nrphone = $data['nrphone'];
    
                $users[] = $user;
            }

            return $users;
        }
        return false;
    }

    public static function validateEmail($email){
        $user = Person::select()->where('desemail', $email)->get();

        if(count($user) > 0){
            return $user;
        }
        
        return false;
    }

    public static function validateLogin($login){
        $user = User::select()->where('deslogin', $login)->get();

        if(count($user) > 0){
            return $user;
        }
        
        return false;
    }


    public static function savePerson($desperson, $desemail, $nrphone){
        Person::insert([
            'desperson'=>$desperson,
            'desemail'=>$desemail,
            'nrphone'=>$nrphone
        ])->execute();

        $person = Person::select()->where('desemail', $desemail)->one();
        if($person){
            return $person;
        }
        return false;
    }

    public static function saveUser($idperson, $deslogin, $despassword, $inadmin = 0 ){
        User::insert([
            'idperson'=>$idperson,
            'deslogin'=>$deslogin,
            'despassword'=>$despassword
        ])->execute();

        $user  = User::select()->where('deslogin', $deslogin)->one();  
        if($user){
            return $user;
        }    
        return false;
    }

    public static function getUserById($iduser){
        $data = User::select()
            ->where('iduser', $iduser)
            ->join('persons', 'users.idperson', '=' , 'persons.idperson')
            ->orderBy('deslogin')
        ->one();

        if($data){
            $user = self::transformArrayToUser($data);

            return $user;
        }

        return false;        
    }

    public static function getPersonById($idperson){
        $data = Person::select()
            ->where('idperson', $idperson)
        ->one();

        if($data){
            $user = $user = new User($data);
            $user->idperson = $data['idperson'];
            $user->desperson = $data['desperson'];
            $user->desemail = $data['desemail'];
            $user->nrphone = $data['nrphone'];
            $user->dtregister = $data['dtregister'];
            
            return $user;
        }      

        return false;;
    }

    /**Auxiliar*/
    private static function transformArrayToUser($data = []){

        if($data){
            $user = new User();

            $user->iduser = $data['iduser'];
            $user->deslogin = $data['deslogin'];
            $user->despassword = $data['despassword'];
            $user->idperson = $data['idperson'];
            $user->desperson = $data['desperson'];
            $user->desemail = $data['desemail'];
            $user->nrphone = $data['nrphone'];
            $user->inadmin = $data['inadmin'];
            $user->dtregister = $data['dtregister'];

            return $user;
        }

        return $data;
    }

    public static function updateUserPerson($user){

        try{
            User::update([
                    'deslogin' => $user->deslogin,
                    'despassword' => $user->despassword,
                    'inadmin' => $user->inadmin
                ])->where('iduser', $user->iduser)
            ->execute();

            Person::update([
                    'desperson' => $user->desperson,
                    'desemail' => $user->desemail,
                    'nrphone' => $user->nrphone ?? NULL
                ])->where('idperson', $user->idperson)
            ->execute();
        }catch(Exception $e){
            echo 'Exceção capturada: ', $e->getMessage(), "\n";
        }

        return true;
    }

    public static function deleteUser($iduser){

        $user = User::select()->where('iduser', $iduser)->execute();

        if(count($user)> 0){
            User::delete()->where('iduser',$user[0]['iduser'])->execute();
            Person::delete()->where('idperson',$user[0]['idperson'])->execute();
        }
    }

    public static function saveNewPersonUser($data = []){

        $_SESSION['registerValues'] = $data;

        if(!isset($data['desperson']) || $data['desperson'] == ''){
            $_SESSION['flash'] = 'Preencha o nome.';
            return false;
        }

        if(!isset($data['desemail']) || $data['desemail'] == ''){
            $_SESSION['flash'] = 'Preencha o Email.';
            return false;
        }

        if(!isset($data['nrphone']) || $data['nrphone'] == ''){
            $_SESSION['flash'] = 'Preencha o Telefone.';
            return false;
        }

        if(!isset($data['despassword']) || $data['despassword'] == ''){
            $_SESSION['flash'] = 'Preencha a senha.';
            return false;
        }

        /*E-MAIL*/
        $email = UserHandler::validateEmail($data['desemail']);
        if($email != false){
            $_SESSION['flash'] = 'E-mail já cadastrado.';
            return false;
        }
        $email = $data['desemail'];

        /**LOGIN */
        $login = UserHandler::validateLogin($data['desemail']);
        if($login != false){
            $_SESSION['flash'] = 'Login não disponível.';
            return false;
        }
        $login = $data['desemail'];

        /*VALIDAR NUMERO DE TELEFONE*/
        $phone = $data['nrphone'];

        $password_hash = password_hash($data['despassword'], PASSWORD_BCRYPT);

        $newPerson = UserHandler::savePerson($data['desperson'], $email, $phone);

        if($newPerson){

            $newUser = UserHandler::saveUser($newPerson['idperson'], $login, $password_hash, $data['inadmin']);

            $user = array_merge($newPerson, $newUser);

        }

        self::verifyLogin($login, $data['despassword']);

        return $user;
    }


    public static function forgotReset($idperson, $password){

        $hash = password_hash($password, PASSWORD_DEFAULT);

        User::update([
            'despassword' => $hash])->where('idperson', $idperson)
        ->execute();

    }

    public static function getOrders($idUser){

        $data = Order::select()
                ->join('ordersstatus','orders.idstatus', '=', 'ordersstatus.idstatus')
                ->join('carts', 'orders.idcart', '=', 'carts.idcart')
                ->join('users', 'orders.iduser', '=', 'users.iduser')
                ->join('addresses', 'orders.idaddress', '=', 'addresses.idaddress')
                ->join('persons', 'users.idperson', '=', 'persons.idperson')
            ->where('users.iduser', $idUser )
        ->get();

    if($data){
        return $data;
    }

    return false;

    }

    public static function updatePass($iduser = '', $newPassword =  ''){

        if(!empty($iduser) && !empty($newPassword)){
        
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            User::update([
                'despassword' => $hash
                ])
                ->where('iduser', $iduser)
            ->execute();

            return true;
        }

        return false;
    }
}