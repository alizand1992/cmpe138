<?php
namespace app\Controllers;

use \app\models\BankAccount as BankAccount;

class BankAccountController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function index($req, $res, $args) {
        $accounts = BankAccount::findUserAccounts($_SESSION["user_id"]);
        $args["accounts"] = (array)$accounts;

        return $this->view->render($res, 'bankAccount/index.html', $args);
    }

    public function new($req, $res, $args) {
        return $this->view->render($res, 'bankAccount/new.html', $args);
    }

    public function edit($req, $res, $args) {
        $data["account"] = BankAccount::find($args["id"]);
        return $this->view->render($res, 'bankAccount/edit.html', $data);
    }

    public function update($req, $res, $args) {
        $data = $req->getParams();
        $account = new BankAccount($data);
        $data["account"] = $account->save();

        if ($data["account"] != false) {
            $data["success"] = "Your changes have been made!";
        } else {
            $data["error"] = "There was an error saving the changes!";
        }

        return $this->view->render($res, 'bankAccount/edit.html', $data);
    }
}
