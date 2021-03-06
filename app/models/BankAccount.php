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

    public function create() {
        $user_id = $_SESSION["user_id"];
        $query = "SELECT port_id FROM traders WHERE user_id='$user_id'";
        $mysqli = Mysqli::mysqli();
        $result = $mysqli->query($query);
        $this->port_id = $result->fetch_array(MYSQLI_ASSOC)['port_id'];

        if ($this->account_no != null && $this->routing_no != null && $this->port_id != null) {
            $query = "INSERT INTO bank_accounts (account_no, routing_no, port_id) " .
                   "VALUES ($this->account_no, $this->routing_no, $this->port_id)";
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

    public static function transfer_to_port($user_id, $amount) {
        $mysqli = Mysqli::mysqli();
        $query = "UPDATE portfolios SET funds = funds + $amount WHERE id=(SELECT port_id FROM traders WHERE user_id='$user_id')";

        return $mysqli->query($query);
    }

    public static function transfer_from_port($user_id, $amount) {
        $mysqli = Mysqli::mysqli();
        $query = "SELECT funds FROM portfolios WHERE id=(SELECT port_id FROM traders WHERE user_id='$user_id')";
        $result = $mysqli->query($query);

        if ($result != null && $amount <= $result->fetch_assoc()["funds"]) {
            $query = "UPDATE portfolios SET funds = funds - $amount WHERE id=(SELECT port_id FROM traders WHERE user_id='$user_id')";
            return $mysqli->query($query);
        }

        return false;
    }
}
