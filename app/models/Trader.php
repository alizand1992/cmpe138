<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("User.php");
require_once("Portfolio.php");

class Trader extends User {
    private $portfolio;

    public function __construct($args) {
        $args["type"] = "Trader";
        parent::__construct($args);
        $this->portfolio = new \app\Models\Portfolio($args["port_id"]);
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
        $mysqli = \app\Models\Mysqli::mysqli();
        $query = "SELECT * FROM users u " .
               "LEFT JOIN traders t ON u.id = t.user_id " .
               "LEFT JOIN portfolios p ON t.port_id = p.id " .
               "WHERE u.id='$id'";
        $result = $mysqli->query($query);
        $mysqli->close();

        $args = $result->fetch_array(MYSQLI_ASSOC);
        // var_dump($args);
        return new Trader($args);
    }

    public function create() {
        $id = parent::create();
        $mysqli = \app\Models\Mysqli::mysqli();

        $query = "INSERT INTO portfolios (funds) " .
               "VALUES (0.0)";
        $mysqli->query($query);

        $query = "INSERT INTO traders (user_id, port_id) " .
               "VALUES ($id, $mysqli->insert_id)";
        $mysqli->query($query);

        $this->portfolios = $mysqli->insert_id;
    }
}
