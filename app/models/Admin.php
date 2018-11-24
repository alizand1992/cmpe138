<?php
namespace app\Models;

require_once("User.php");

class Admin extends User {
    private $portfolios;

    function __construct(array $args) {
        $args["type"] = "Admin";
        parent::_construct($args);
        __set($args["portfolio"]);
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
