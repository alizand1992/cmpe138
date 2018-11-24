<?php
namespace app\Controllers;

class UserController {
    protected $view;
    protected $db;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function login($request, $response, $args) {
        return $this->view->render($response, 'user/login.html', $args);
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

        $mysqli = new \mysqli('localhost', 'se_user', 'se_user_password', 'stock_exchange');

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
        $username = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $mysqli = new \mysqli('localhost', 'se_user', 'se_user_password', 'stock_exchange');

        if ($mysqli->connect_errno) {
            return $res->withRedirect("login?errorno=$mysqli->connect_errno");
        }

        $result = $mysqli->query($query);
        // error=1 user not found
        if ($result->num_rows == 0) {
            $mysqli->close();
            $data["error"] = "Incorrect username or password.";
            return $this->login($req, $res, $data);
        }

        $_SESSION['user_id'] = $mysqli->fetch_array(MYSQL_ASSOC)['id'];
        $mysqli->close();
        return $res->withRedirect("profile");
    }
}
