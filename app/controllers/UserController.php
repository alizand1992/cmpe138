<?php
namespace app\Controllers;

use \app\models\User as User;
use \app\models\Trader as Trader;
use \app\models\Admin as Admin;

class UserController {
    protected $view;
    protected $logger;

    public function __construct(\Slim\Views\Twig $view, \Monolog\Logger $logger) {
        $this->view = $view;
        $this->logger = $logger;
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
            $this->logger->addInfo($data["error"]);
            return $this->register($req, $res, $data);
        }

        if (User::exists($data['username'])) {
            $data["error"] = "The email is already registered.";
            $this->logger->addInfo($data["error"]);
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
        $query = "SELECT * FROM users WHERE username='$username'";
        $mysqli = new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");

        if ($mysqli->connect_errno) {
            $this->logger->addInfo("login?errorno=$mysqli->connect_errno");
            return $res->withRedirect("login?errorno=$mysqli->connect_errno");
        }

        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if ($result->num_rows == 0 || !password_verify(trim($data["password"]), $row["password"])) {
            $mysqli->close();
            $data["error"] = "Incorrect username or password.";
            $this->logger->addInfo($data["error"]);
            return $this->login($req, $res, $data);
        }

        $_SESSION["user_id"] = $row["id"];
        $mysqli->close();
        return $res->withRedirect("profile");
    }

    public function profile($req, $res, $args) {
        $user_arr = $this->getUserArray($_SESSION["user_id"]);

        return $this->view->render($res, 'user/profile.html', $user_arr);
    }

    public function edit($req, $res, $args) {
        $user_arr = $this->getUserArray($_SESSION["user_id"]);

        return $this->view->render($res, 'user/edit.html', $user_arr);
    }

    public function update($req, $res, $args) {
        $data = $req->getParams();
        $data["id"] = $_SESSION["user_id"];
        $user = new User($data);

        if ($user->save()) {
            $data["success"] = "The user was saved successfully";
            $this->logger->addInfo($data["success"]);
        } else {
            $data["error"] = "There was an error making your changes!";
            $this->logger->addInfo($data["error"]);
        }
        return $this->view->render($res, 'user/edit.html', $data);
    }

    private function getUserArray($id) {
        $user = null;
        if (User::isTrader($_SESSION["user_id"])) {
            $user = Trader::find($_SESSION["user_id"]);
        } else {
            $user = Admin::find($_SESSION["user_id"]);
        }
        $user_arr = (array)$user;
        $user_arr["portfolio"] = (array)$user_arr["\0app\\Models\\Trader\0portfolio"];
        unset($user_arr["app\\Models\\Trader\0portfolio"]);
        return $user_arr;
    }
}
