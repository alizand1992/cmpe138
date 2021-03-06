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
        $mysqli = Mysqli::mysqli();
        $user_id = $_SESSION["user_id"];
        $this->label = strtoupper($this->label);
        $result = $mysqli->query("SELECT id FROM stocks WHERE label='$this->label'");
        $stock_id = $result->fetch_assoc()['id'];
        $result = $mysqli->query("SELECT port_id FROM traders WHERE user_id='$user_id'");
        $this->port_id = $result->fetch_assoc()['port_id'];
        $result = $mysqli->query("SELECT * FROM stocks_to_sell " .
                                 "WHERE stock_id=$stock_id " .
                                 "AND price <= $this->price " .
                                 "ORDER BY price");

        if (!$result) {
            return "No Such Stock found ($this->label)!";
        }

        $to_sell = null;

        $this->to_buy = $this->quantity;

        while ($row = $result->fetch_assoc()) {
            if ($row['price'] <= $this->price) {
                $temp_stock = new StockToBuy($row);
                if ($this->to_buy >= $temp_stock->quantity) {
                    $temp_stock->to_buy = $temp_stock->quantity;
                    $this->to_buy -= $this->quantity;
                } else {
                    $temp_stock->to_buy = $this->to_buy;
                    $this->to_buy = 0;
                }

                $temp_stock->buy_from_port();

                if ($this->to_buy == 0) {
                    break;
                }
            }
        }

        if ($this->to_buy > 0) {
            $mysqli->query("INSERT INTO stocks_to_buy (stock_id, port_id, quantity, price) " .
                           "VALUES ('$stock_id', '$this->port_id', '$this->to_buy', '$this->price')");
        }

        return "The buy order for ($this->label) has been added!";
    }

    public function buy_from_port() {
        $abort = false;
        $error = null;

        $seller_port = $this->port_id;

        $mysqli = Mysqli::mysqli();
        $user_id = $_SESSION["user_id"];
        $mysqli->autocommit(false);
        $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        $result = $mysqli->query("SELECT port_id FROM traders WHERE user_id='$user_id'");
        $buyer_port = $result->fetch_assoc()['port_id'];

        $funds = $mysqli->query("SELECT funds FROM portfolios where id='$buyer_port'")->fetch_assoc()['funds'];

        $query = "SELECT * FROM stocks_to_sell WHERE id='$this->id'";
        $result = $mysqli->query($query);

        if ($result->num_rows == 0) {
            $error = "The requested stock is no longer available. ($this->label)";
            $abort = true;
        }

        // MAX AVAILABLE TO BUY
        $max_quan_avail = $result->fetch_assoc()['quantity'];

        if ($this->quantity > $max_quan_avail) {
            $this->to_buy = $max_quan_avail;
        } else {
            $this->to_buy = $this->quantity;
        }

        $total_price = $this->price * $this->to_buy;

        if ($total_price > $funds) {
            $error = "Insufficient Funds.";
            $abort = true;
        }

        // SELLER AVAIALIBITY
        $total_seller_query = "SELECT id, quantity FROM portfolio_stocks " .
                            "WHERE port_id='$seller_port' " .
                            "AND stock_id='$this->stock_id'";

        $result = $mysqli->query($total_seller_query);
        $temp = $this->to_buy;
        $delete_from = null;

        while ($row = $result->fetch_assoc()) {
            if ($temp == 0) break;

            if ($temp - $row["quantity"] >= 0) {
                $delete_from[] = [$row["id"], 0];
            } else {
                $delete_from[] = [$row["id"], $row["quantity"] - $temp];
            }

            $temp -= $row["quantity"];
        }


        if ($this->to_buy == $max_quan_avail) {
            $mysqli->query("DELETE FROM stocks_to_sell WHERE id='$this->id'");
        } else {
            $rem = $max_quan_avail - $this->to_buy;
            $mysqli->query("UPDATE stocks_to_sell SET quantity='$rem' WHERE id='$this->id'");
        }

        foreach ($delete_from as $row) {
            $row_id = $row[0];
            if ($row[1] == 0) {
                $mysqli->query("DELETE FROM portfolio_stocks WHERE id='$row_id'");
            } else {
                $mysqli->query("UPDATE portfolio_stocks SET quantity='$row[1]' WHERE id='$row_id'");
            }
        }

        $mysqli->query("INSERT INTO portfolio_stocks (stock_id, port_id, price, quantity)" .
                       "VALUES ('$this->stock_id', '$buyer_port', '$this->price', '$this->to_buy')");

        $mysqli->query("UPDATE portfolios SET funds = funds - $total_price where id='$buyer_port'");

        $mysqli->query("UPDATE portfolios SET funds = funds + $total_price where id='$seller_port'");

        if (!$abort) {
            $mysqli->commit();
        }

        $mysqli->close();
        return $error;
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

    public static function buy_orders() {
        $mysqli = Mysqli::mysqli();
        $user_id = $_SESSION['user_id'];
        $result = $mysqli->query("SELECT port_id FROM traders WHERE user_id='$user_id'");
        $port_id = $result->fetch_assoc()['port_id'];
        $result = $mysqli->query("SELECT stb.id, s.label, s.company_name, stb.quantity, stb.price FROM stocks_to_buy stb " .
                                 "JOIN stocks s ON stb.stock_id = s.id " .
                                 "WHERE port_id='$port_id'");
        var_dump($result);
        return $result;
    }
}
