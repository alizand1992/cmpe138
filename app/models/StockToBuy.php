<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("Stock.php");

class StockToBuy extends Stock {
    public $id;
    public $port_id;
    public $to_buy;

    public function __construct($args) {
        parent::__construct($args);
        $this->id = $args["id"];
        $this->port_id = $args["port_id"];
    }

    public function buy() {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT * FROM stocks_to_sell " .
               "WHERE stock_id='$this->stock_id' " .
               "AND id='$this->id'";

        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);

        if ($result->num_rows == 0) {
            return false;
        }

        $max_quantity = $result->fetch_array(MYSQLI_ASSOC)["quantity"];

        $this->to_buy = $this->quantity;
        if ($this->quantity > $max_quantity) {
            $this->to_buy = $max_quantity;
        }

        $query = "SELECT port_id FROM traders WHERE user_id='$user_id'";
        $current_port_id = $mysqli->query($query)->fetch_array(MYSQLI_ASSOC)["port_id"];

        $query_1 = "";
        $query_2 = "";

        if ($this->to_buy == $max_quantity) {
            $query_1 = "DELETE FROM stocks_to_sell " .
                     "WHERE id='$this->id'";
            $query_2 = "UPDATE portfolio_stocks SET quantity='' " .
                     "WHERE stock_id='$this->stock_id' " .
                     "AND port_id='$this->port_id'";
        } else {
            $remainder = $max_quantity - $this->to_buy;
            $query_1 = "UPDATE stocks_to_sell SET quantity='$remainder' " .
                     "WHERE id='$this->id'";
        }

        $query_3 = "INSERT INTO portfolio_stocks (stock_id, port_id, price, quantity) " .
                     "VALUES ('$this->stock_id', '$current_port_id', '$this->price', '$this->to_buy')";

        // Start transaction for stock transfer
        $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $mysqli->query($query_1);
        $mysqli->query($query_3);
        $mysqli->commit();

        $mysqli->close();

        if ($mysqli->errno) {
            return false;
        }
        return true;
    }

    // Statics
    public static function available_now() {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT sts.stock_id, s.label, s.company_name, sts.price, sts.quantity, sts.id, sts.port_id FROM stocks_to_sell sts " .
               "JOIN stocks s ON sts.stock_id = s.id " .
               "WHERE sts.port_id NOT IN (SELECT port_id FROM traders WHERE user_id='$user_id')";
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);

        $available_now = null;

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $available_now[] = (array)(new StockToBuy($row));
        }

        return $available_now;
    }
}
