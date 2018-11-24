<?php
namespace app\Models;

require_once("User.php");

class Admin extends User {
    private $portfolios;

    function __construct(array $args) {
        $args["type"] = "Admin";
        parent::__construct($args);
        $this->portfolios = "";
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

    public static function find($id) {
        $mysqli = self::mysqli();
        $query = "SELECT * FROM users u " .
               "LEFT JOIN admins a ON u.id = a.user_id " .
               "WHERE u.id='$id'";
        $result = $mysqli->query($query);
        $mysqli->close();

        $args = $result->fetch_array(MYSQLI_ASSOC);
        return new Admin($args);
    }
}
