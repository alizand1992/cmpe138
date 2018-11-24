<?php
namespace app\Controllers;

use \app\models\User as User;
use \app\models\Trader as Trader;
use \app\models\Admin as Admin;



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

    public function createUser($req, $res, $args) {
        $data = $req->getParams();

        if ($data["password"] != $data["re_password"]) {
            $data["error"] = "Passwords do not match!";
            return $this->register($req, $res, $data);
        }

        if (User::exists($data['username'])) {
             $data["error"] = "The email is already registered.";
            return $this->register($req, $res, $data);
        }

        $user = new Trader($data);
        $user->create();
        $_SESSION['user_id'] = $user->id;

        return $res->withRedirect("profile");
    }

    public function createSession($req, $res, $args) {
        $data = $req->getParams();
        $username = $data["username"];
        $password = password_hash(trim($data["password"]), PASSWORD_DEFAULT);

        $query = "SELECT * FROM users WHERE username='$username'";
        $mysqli = new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");

        if ($mysqli->connect_errno) {
            return $res->withRedirect("login?errorno=$mysqli->connect_errno");
        }

        $result = $mysqli->query($query);

        if ($result->num_rows == 0) {
            $mysqli->close();
            $data["error"] = "Incorrect username or password.";
            return $this->login($req, $res, $data);
        }

        $_SESSION["user_id"] = $result->fetch_array(MYSQLI_ASSOC)["id"];
        $mysqli->close();
        return $res->withRedirect("profile");
    }

    public function profile($req, $res, $args) {
        $user = null;
        if (User::isTrader($_SESSION["user_id"])) {
            $user = Trader::find($_SESSION["user_id"]);
        } else {
            $user = Admin::find($_SESSION["user_id"]);
        }

        // var_dump((array)$user);
        // var_dump($_SESSION["user_id"]);
        return $this->view->render($res, 'user/profile.html', (array)$user);
    }
}
