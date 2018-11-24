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

    public function createUser($req, $res, $args) {
        $data = $req->getParams();
        if ($data["password"] != $data["re_password"]) {
            $data["error"] = "Passwords do not match!";
            return $this->register($req, $res, $data);
        }

        $mysqli = new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");

        if ($mysqli->connect_errno) {
            $data["error"] = $mysqli->error;
            return $this->register($req, $res, $data);
        }

        $email = $data['email'];
        $query = "SELECT * FROM users WHERE username='$email'";
        $result = $mysqli->query($query);

        if ($result->num_rows != 0) {
            $mysqli->close();
            $data["error"] = "The email is already registered.";
            return $this->register($req, $res, $data);
        }

        $password = password_hash($data["password"], PASSWORD_DEFAULT);
        $f_name = $data["f_name"];
        $l_name = $data["l_name"];
        $bday = $data["bday"];

        $query = "INSERT INTO users (username, password, f_name, l_name, bday) " .
               "VALUES ('$email', '$password', '$f_name', '$l_name', '$bday')";

        $mysqli->query($query);

        return $res->withRedirect("profile");
    }

    public function createSession($req, $res, $args) {
        $data = $req->getParams();
        $username = $data["email"];
        $password = password_hash(trim($data['password']), PASSWORD_DEFAULT);

        $query = "SELECT * FROM users WHERE username='$username'";
        $mysqli = new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");

        if ($mysqli->connect_errno) {
            return $res->withRedirect("login?errorno=$mysqli->connect_errno");
        }

        $result = $mysqli->query($query);
        var_dump($result);
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
        $user_id = $_SESSION["user_id"];
        $user_query = "SELECT * FROM users WHERE id='$user_id'";
        $mysqli = new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");

        $result = $mysqli->query($user_query);
        $args = $result->fetch_array(MYSQLI_ASSOC);
        $args["type"] = "Trader";
        return $this->view->render($res, 'user/profile.html', $args);
    }
}
