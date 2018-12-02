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

/////////////////////////////////////////////////////////////////////////////
///
/// function sell()
/// fullfills a user's sell request.
///
/////////////////////////////////////////////////////////////////////////////

    public function sell() {
        // Logic to check if sell request is valid, (by checking if stock is currently in portfilio and has the right quantity) then checks for buy orders to fill, filling them from highest price to lowest. Then it will post a sell order.

        // MAX AVAILABLE TO SELL
        $max_stock_to_sell = $mysqli->query("SELECT quantity FROM portfolio_stocks 
            WHERE port_id='$this->port_id' AND stock_id='$this->stock_id'");

        $stock_left_to_sell = min($max_stock_to_sell, $quant_to_sell);

        //GET ALL CURRENT BUY ORDERS FOR THIS STOCK, SORTED BY HIGHEST PRICE FIRST
        $buy_orders = $mysqli->query("SELECT stb.stock_id, stb.label, stb.company_name, stb.price, stb.quantity, stb.id, stb.port_id 
            FROM stocks_to_buy stb 
            WHERE stb.stock_id='$this->stock_id' AND stb.port_id <> '$this->port_id' 
            ORDER BY stb.price DESC");

        $buy_order_arr = null;

        while ($row = $buy_orders->fetch_array(MYSQLI_ASSOC)) {
            $buy_order_arr[] = (array)(new StockToSell($row));
        }

        //FILL AS MANY BUY ORDERS AS POSSIBLE, Iterate through, playing buy orders
        foreach($buy_order_arr as $buy_order){
            if ($stock_left_to_sell >  0){
                $current_sell_quantity = min($stock_left_to_sell, $buy_order->quantity);
                if(fullfill_buy_order($buy_order->port_id, $current_sell_quantity)==null){
                    $stock_left_to_sell -= $current_sell_quantity;
                }                
            }
        }
        //CREATE SELL ORDER FOR ANY REMAINING STOCK
        $this->quantity = $current_sell_quantity;
        if ($this->quantity > 0){
            create_sell_order();
        }
    }

/////////////////////////////////////////////////////////////////////////////
///
/// function create_sell_order()
/// creates a sell order for all stocks leftover after filling buy orders.
///
/////////////////////////////////////////////////////////////////////////////

    //Insert sell_order into stocks_to_sell table. 
    public static function create_sell_order() {
        
        $mysqli = Mysqli::mysqli();
        $query = "INSERT INTO stocks_to_sell (stock_id, port_id, quantity, price) " . 
        "VALUES ('$this->stock_id', '$this->port_id', '$this->quantity', '$this->price')";
        
        $mysqli->query($query);
        $this->id = $mysqli->insert_id;
        return $this->id;
    }

/////////////////////////////////////////////////////////////////////////////
///
/// function fullfill_buy_order()
/// Transfers a quantity ($sell_quantity) of stocks belonging to this object's 
/// porfolio to a buyer's ($buyer_port_id) portfolio, and moves funds accordingly. 
///
/////////////////////////////////////////////////////////////////////////////

    public function fullfill_buy_order($buyer_port_id, $sell_quantity) {
        $abort = false;
        $error = null;

        $seller_port = $this->port_id;

        $mysqli = Mysqli::mysqli();
        $user_id = $_SESSION["user_id"];

        //BEGIN TRANSACTION
        $mysqli->autocommit(false);
        $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // $result = $mysqli->query("SELECT port_id FROM traders WHERE user_id='$user_id'");
        // $buyer_port = $result->fetch_assoc()['port_id'];

        $funds = $mysqli->query("SELECT funds FROM portfolios where id='$buyer_port_id'")->fetch_assoc()['funds'];

        $query = "SELECT * FROM stocks_to_buy WHERE port_id='$buyer_port_id'";
        $result = $mysqli->query($query);

        if ($result->num_rows == 0) {
            $error = "The requested stock is no longer has any buy orders. ($this->label)";
            $abort = true;
        }

        // MAX AVAILABLE TO SELL
        $max_to_sell_avail = $result->fetch_assoc()['quantity'];

        // if ($this->quantity > $max_quan_avail) {
        //     $this->to_buy = $max_quan_avail;
        // } else {
        //     $this->to_buy = $this->quantity;
        // }

        $total_price = $this->price * $sell_quantity;

        if ($total_price > $funds) {
            $error = "Buyer does not have sufficient Funds.";
            $abort = true;
        }

        // SELLER AVAIALIBITY
        $total_seller_query = "SELECT id, quantity FROM portfolio_stocks " .
                            "WHERE port_id='$this->port_id' " .
                            "AND stock_id='$this->stock_id'";

        $result = $mysqli->query($total_seller_query);
        $temp = $sell_quantity;
        $delete_from = null;

        while ($row = $result->fetch_assoc()) {
            if ($temp == 0) break;

            if ($temp - $row["quantity"] >= 0) {
                $delete_from[] = [$row["id"], 0];
            } else {
                $delete_from[] = [$row["id"], $row["quantity"] - $sell_quantity];
            }
            $temp -= $row["quantity"];
        }

        //Update buy orders.
        if ($sell_quantity == $max_quan_avail) {
            $mysqli->query("DELETE FROM stocks_to_buy WHERE id='$this->id'");
        } else {
            $rem = $max_quan_avail - $sell_quantity;
            $mysqli->query("UPDATE stocks_to_buy SET quantity='$rem' WHERE id='$this->id'");
        }

        //Reduce quantity of stocks in seller's portfolio.
        foreach ($delete_from as $row) {
            $row_id = $row[0];
            if ($row[1] == 0) {
                $mysqli->query("DELETE FROM portfolio_stocks WHERE id='$row_id'");
            } else {
                $mysqli->query("UPDATE portfolio_stocks SET quantity='$row[1]' WHERE id='$row_id'");
            }
        }

        //Plack stocks into buyer's portfolio.
        $mysqli->query("INSERT INTO portfolio_stocks (stock_id, port_id, price, quantity)" .
                       "VALUES ('$this->stock_id', '$buyer_port_id', '$this->price', '$sell_quantity')");

        //Update funds in each portfolio.
        $mysqli->query("UPDATE portfolios SET funds = funds - $total_price where id='$buyer_port_id'");

        $mysqli->query("UPDATE portfolios SET funds = funds + $total_price where id='$this->port_id'");

        if (!$abort) {
            $mysqli->commit();
        }
        //END TRANSACTION

        $mysqli->close();
        return $error;
    }
}
