<?php
namespace app\Models;

require_once("Mysqli.php");

class BankAccount {
    public $id;
    public $account_no;
    public $routing_no;
    public $port_id;

    public function __construct($args) {
        foreach ($args as $value) {
            $this->__set(array_search($value, $args), $value);
        }
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

    public static function findUserAccounts($user_id) {
        $query = "SELECT * FROM bank_accounts " .
               "WHERE port_id " .
                   "IN (SELECT port_id FROM traders WHERE user_id='$user_id')";
        $mysqli = \app\Models\Mysqli::mysqli();
        $result = $mysqli->query($query);

        $accounts = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $accounts[] = (array)(new BankAccount($row));
        }

        return $accounts;
    }
}
