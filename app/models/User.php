<?php
namespace app\Models;
// User Fields
// username
// password
// screen_name
// f_name
// l_name
// bday

require_once("Mysqli.php");

class User {
    public $id;
    public $username;
    public $password;
    public $screen_name;
    public $f_name;
    public $l_name;
    public $bday;
    public $type;

    public function __construct($args) {
        foreach ($args as $value) {
            $this->__set(array_search($value, $args), $value);
        }

        if ($this->id != null && $this->username == null) {
            $mysqli = Mysqli::mysqli();
            $result = $mysqli->query("SELECT * FROM users WHERE id='id'");
            this($result->fetch_assoc()[0]);
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

    public function save() {
        if ($this->username == null ||
            $this->f_name == null || $this->l_name == null || $this->bday == null) {
            return false;
        }

        $mysqli = Mysqli::mysqli();
        $query = "SELECT username FROM users where username='$this->username' and id <> '$this->id'";
        $username_taken = $mysqli->query($query)->num_rows != 0;

        if ($username_taken) {
            $mysqli->close();
            return $mysqli->error;
        }

        $query = "UPDATE users set " .
               "username = '$this->username', " .
               "screen_name = '$this->screen_name', " .
               "f_name = '$this->f_name', " .
               "l_name = '$this->l_name', " .
               "bday = '$this->bday' " .
               "WHERE id = '$this->id'";
        $mysqli->query($query);
        $mysqli->close();

        if ($mysqli->errno) {
            return $mysqli->error;
        }
        return true;
    }

    public function create() {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $mysqli = \app\Models\Mysqli::mysqli();
        $query = "INSERT INTO users (username, password, f_name, l_name, bday) " .
               "VALUES ('$this->username', '$this->password', '$this->f_name', '$this->l_name', '$this->bday')";

        $mysqli->query($query);
        $this->id = $mysqli->insert_id;
        return $this->id;
    }

    // Statics
    public static function exists($username) {
        $mysqli = \app\Models\Mysqli::mysqli();
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = $mysqli->query($query);
        $mysqli->close();

        if ($result->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function isTrader($id) {
        $query = "SELECT COUNT(*) FROM traders WHERE user_id='$id' GROUP BY user_id";
        return self::getCount($query);
    }

    public static function isAdmin($id) {
        $query = "SELECT COUNT(*) FROM admins WHERE user_id='$id' GROUP BY user_id";
        return self::getCount($query);
    }

    private static function getCount($query) {
        $mysqli = \app\Models\Mysqli::mysqli();
        return $mysqli->query($query)->num_rows != 0;
    }

    public static function find($id) {
        $mysqli = \app\Models\Mysqli::mysqli();
        $query = "SELECT * FROM users u " .
               "WHERE u.id='$id'";
        $result = $mysqli->query($query);
        $mysqli->close();

        $args = $result->fetch_array(MYSQLI_ASSOC);
        return new User($args);
    }
}
