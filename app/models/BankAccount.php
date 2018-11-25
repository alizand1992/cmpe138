<?php
namespace app\Models;

use \app\Models\Mysqli as Mysqli;

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

    public function save() {
        if ($this->id != null && $this->account_no != null && $this->routing_no != null) {
            $query = "UPDATE bank_accounts SET " .
                   "account_no='$this->account_no', " .
                   "routing_no='$this->routing_no' " .
                   "WHERE id='$this->id'";
            $mysqli = Mysqli::mysqli();
            $mysqli->query($query);
            return $this;
        }
        return false;
    }

    // Statics
    public static function findUserAccounts($user_id) {
        $query = "SELECT * FROM bank_accounts " .
               "WHERE port_id " .
                   "IN (SELECT port_id FROM traders WHERE user_id='$user_id')";
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);

        $accounts = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $accounts[] = (array)(new BankAccount($row));
        }

        return $accounts;
    }

    public static function find($id) {
        $query = "SELECT * FROM bank_accounts WHERE id='$id'";
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        return (array)(new BankAccount($row));
    }
}
