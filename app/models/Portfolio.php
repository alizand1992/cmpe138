<?php
namespace app\Models;

require_once("Mysqli.php");
require_once("Stock.php");

class Portfolio {
    public $stocks;
    public $funds;

    public function __construct($args) {
        $query = "SELECT funds FROM portfolios WHERE id='$args'";
        $mysqli = \app\Models\Mysqli::mysqli();
        $result = $mysqli->query($query);
        $this->funds = $result->fetch_array(MYSQLI_NUM)[0];

        $query = "SELECT * FROM portfolio_stocks ps " .
               "JOIN stocks s ON ps.stock_id = s.id " .
               "WHERE ps.port_id='$args'";
        $result = $mysqli->query($query);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->stocks[] = new Stock($row);
        }
    }

    public static function getFunds($user_id) {
        $mysqli = \app\Models\Mysqli::mysqli();
        $result = $mysqli->query("SELECT funds FROM portfolios WHERE id=(SELECT port_id FROM traders WHERE user_id='$user_id')");

        if ($result != false) {
            return $result->fetch_assoc()['funds'];
        }

        return -1;
    }
}
