<?php
namespace app\Models;
// User Fields
// username
// password
// screen_name
// f_name
// l_name
// bday

class User {
    protected $id;
    protected $username;
    protected $password;
    protected $screen_name;
    protected $f_name;
    protected $l_name;
    protected $bday;
    protected $type;

    public function __construct($args) {
        foreach ($args as $value) {
            $this->__set(array_search($value, $args), $value);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    public static function mysqli() {
        return new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");
    }

    public static function find($id) {
        $mysqli = self::mysqli();
        $query = "SELECT * FROM users u " .
               "LEFT JOIN traders t ON u.id = t.user_id " .
               "LEFT JOIN admins a ON u.id = a.user_id " .
               "LEFT JOIN portfolios p ON t.port_id = p.id " .
               "WHERE u.id='$id'";
        $result = $mysqli->query($query);
        $mysqli->close();

        $args = $result->fetch_array(MYSQLI_ASSOC);
        return new User($args);
    }

    public static function exists($username) {
        $mysqli = self::mysqli();
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = $mysqli->query($query);
        $mysqli->close();

        if ($result->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function create() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $mysqli = self::mysqli();
        $query = "INSERT INTO users (username, password, f_name, l_name, bday) " .
               "VALUES ('$this->username', '$this->password', '$this->f_name', '$this->l_name', '$this->bday')";

        $mysqli->query($query);
        $this->id = $mysqli->insert_id;
    }
}
