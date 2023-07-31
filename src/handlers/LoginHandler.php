<?php
namespace src\handlers;

class LoginHandler {

    public static function checkLogin() {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0) {

                $loggedUser = new User();
                $loggedUser->iduser = $data['iduser'];
                $loggedUser->name = $data['name'];
                $loggedUser->inadmin = $data['inadmin'];

                return $loggedUser;
            }
        }

        return false;
    }
}