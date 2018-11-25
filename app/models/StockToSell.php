<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("Stock.php");

class StockToSell extends Stock {
    public $id;
    public $port_id;
    public $stock_id;
    public $price;
    public $quantity;

    public function __construct($args) {
        parent::__construct($args);
        $this->id = $args["id"];
        $this->port_id = $args["port_id"];
    }

    public function sell() {
        // Logic to check if sell request is valid, (by checking if stock is currently in portfilio and has the right quantity) then calls create_sell_order() if it is valid.
    }

    //Insert sell_order into stocks_to_sell table. 
    public static function create_sell_order() {
        
        $mysqli = Mysqli::mysqli();
        $query = "INSERT INTO stocks_to_sell (stock_id, port_id, quantity, price) " . "VALUES ('$this->stock_id', '$this->port_id', '$this->quantity', '$this->price')";
        $mysqli->query($query);
        $this->id = $mysqli->insert_id;
        return $this->id;
    }
}
