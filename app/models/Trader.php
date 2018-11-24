<?php
namespace app\Models;

require_once("User.php");

class Trader extends User {
    private $portfolio;

    public function __construct($args) {
        $args["type"] = "Trader";
        parent::__construct($args);
        $this->portfolio = $args["portfolio"];
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
}
