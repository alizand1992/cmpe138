<?php
namespace app\Models;

require_once("Mysqli.php");

class Stock {
    public $stock_id;
    public $label;
    public $company_name;
    public $price;
    public $quantity;

    public function __construct($args) {
        foreach ($args as $value) {
            $this->__set(array_search($value, $args), $value);
        }

        // Needs to be overwritten from the loop because loop grabs the first key with the value
        // given and if for example quantity is same as id then quantity will never be written by
        // the loop.

        $this->stock_id = $args["stock_id"];
        $this->quantity = $args["quantity"];
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

    public static function all_stocks() {
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query("SELECT * FROM stocks");

        return $result;
    }
}
