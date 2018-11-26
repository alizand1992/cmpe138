<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("Stock.php");

class StockToSell extends Stock {
    public $id;
    public $port_id;
    public $min_price;
    public $quant_to_sell;

    public function __construct($args) {
        parent::__construct($args);
        $this->id = $args["id"];
        $this->port_id = $args["port_id"];
        $this->$min_price = $args["min_price"];
        $this->$quant_to_sell = $args["quant_to_sell"]; 
    }

    public function sell() {
        // Logic to check if sell request is valid, (by checking if stock is currently in portfilio and has the right quantity) then checks for buy orders to fill, filling them from highest price to lowest. Then it will post a sell order.

        // MAX AVAILABLE TO SELL
        $max_stock_to_sell = $mysqli->query("SELECT quantity FROM portfolio_stocks 
            WHERE port_id='$this->port_id' AND stock_id='$this->stock_id'");

        $stock_left_to_sell = min($max_stock_to_sell, $quant_to_sell);

        //GET ALL CURRENT BUY ORDERS FOR THIS STOCK, SORTED BY HIGHEST PRICE FIRST
        $buy_orders = $mysqli->query("SELECT stb.stock_id, s.label, s.company_name, stb.price, stb.quantity, stb.id, stb.port_id 
            FROM stocks_to_buy stb 
            WHERE stb.stock_id='$this->stock_id' AND stb.port_id <> '$this->port_id' 
            ORDER BY stb.price DESC");

        buy_order_arr = null;

        while ($row = $buy_orders->fetch_array(MYSQLI_ASSOC)) {
            $buy_order_arr[] = (array)(new StockToSell($row));
        }

        //FILL AS MANY BUY ORDERS AS POSSIBLE


        //CREATE SELL ORDER FOR REMAINING STOCK


    }

    //Insert sell_order into stocks_to_sell table. 
    public static function create_sell_order() {
        
        $mysqli = Mysqli::mysqli();
        $query = "INSERT INTO stocks_to_sell (stock_id, port_id, quantity, price) " . 
        "VALUES ('$this->stock_id', '$this->port_id', '$this->quantity', '$this->price')";
        
        $mysqli->query($query);
        $this->id = $mysqli->insert_id;
        return $this->id;
    }
}
