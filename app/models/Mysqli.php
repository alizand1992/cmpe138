<?php
namespace app\Models;

class Mysqli {
    public $mysqli;

    public function __construct($args) {
        $this->mysqli = self::mysqli();
    }

    public static function mysqli() {
        return new \mysqli("localhost", "se_user", "se_user_password", "stock_exchange");
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
