<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("Stock.php");

class StockToBuy extends Stock {
    public $id;
    public $port_id;

    public function __construct($args) {
        parent::__construct($args);
        $this->id = $args["id"];
        $this->port_id = $args["port_id"];
    }

    public function buy() {

    }

    // Statics
    public static function available_now() {
        $query = "SELECT sts.stock_id, s.label, s.company_name, sts.price, sts.quantity, sts.id, sts.port_id FROM stocks_to_sell sts " .
               "JOIN stocks s ON sts.stock_id = s.id";
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);

        $available_now = null;

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $available_now[] = (array)(new StockToBuy($row));
        }

        return $available_now;
    }
}
