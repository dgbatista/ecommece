<?php
namespace src\handlers;

use \src\models\User;

class LoginHandler {

    public static function checkLogin() {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0) {

                $loggedUser = new User();
                $loggedUser->iduser = $data['iduser'];
                $loggedUser->inadmin = $data['inadmin'];

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

                return $dataUser;
            }
        }

        return false;

    }
}