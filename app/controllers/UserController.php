<?php

namespace app\Controllers;

class UserController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function login($req, $res, $args) {
        return $this->view->render($res, 'user/login.html', []);
    }

    public function register($req, $res, $args) {
        return $this->view->render($res, 'user/register.html', []);
    }
}
