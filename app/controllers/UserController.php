<?php
namespace app\Controllers;

class UserController {
    protected $view;
    protected $db;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function login($req, $res, $args) {
        return $this->view->render($res, 'user/login.html', $args);
    }

    public function register($req, $res, $args) {
        return $this->view->render($res, 'user/register.html', $args);
    }

    public function session_create($req, $res, $args) {
        $username = $args['email'];
        $password = password_hash( $args['password'], PASSWORD_DEFAULT);

        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $mysqli = new \mysqli('localhost', 'se_user', 'se_user_password', 'stock_exchange');

        if ($mysqli->connect_errno) {
            return $res->withRedirect("login?errorno=$mysqli->connect_errno");
        }


        $mysqli->query($query);
    }
}
