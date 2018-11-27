<?php
namespace app\Controllers;

use \app\models\BankAccount as BankAccount;

class BankAccountControllerHelper {
    static function logger($filename, $data) {
        $current = file_get_contents($filename);
        $current .= $data . "\n";
        file_put_contents($filename, $current);
    }
}

class BankAccountController {
    protected $view;
    $fname = '../logfile.txt';

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function index($req, $res, $args) {
        $accounts = BankAccount::findUserAccounts($_SESSION["user_id"]);
        $args["accounts"] = (array)$accounts;
        foreach ($accounts as &$a) {
            BankAccountControllerHelper::logger($fname, $a);
        }
        return $this->view->render($res, 'bankAccount/index.html', $args);
    }

    public function new($req, $res, $args) {
        return $this->view->render($res, 'bankAccount/new.html', $args);
    }

    public function edit($req, $res, $args) {
        $data["account"] = BankAccount::find($args["id"]);
        BankAccountControllerHelper::logger($fname, $data["account"]);
        return $this->view->render($res, 'bankAccount/edit.html', $data);
    }

    public function update($req, $res, $args) {
        $data = $req->getParams();
        $account = new BankAccount($data);
        $data["account"] = $account->save();
        BankAccountControllerHelper::logger($fname, $data["account"]);
        $this::redirect_to_edit($req, $res, $data);
    }

    public function create($req, $res, $args) {
        $data = $req->getParams();
        $account = new BankAccount($data);
        $data["account"] = $account->create();
        BankAccountControllerHelper::logger($fname, $data["account"]);
        $this::redirect_to_edit($req, $res, $data);
    }

    private function redirect_to_edit($req, $res, $data) {
        if ($data["account"] != false) {
            $data["success"] = "Your changes have been made!";
            BankAccountControllerHelper::logger($fname, $data["success"]);
        } else {
            $data["error"] = "There was an error saving the changes!";
            BankAccountControllerHelper::logger($fname, $data["error"]);
        }

        return $this->view->render($res, 'bankAccount/edit.html', $data);
    }
}
