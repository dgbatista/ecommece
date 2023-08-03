<?php 
namespace src\controllers;

use \core\Controller;

class MessagesController extends Controller{

    public function __construct($className, $msg) {
        $this->render($className, [
            'flash', $msg
        ]
        );
    }
}
?>