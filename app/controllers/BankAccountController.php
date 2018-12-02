<?php
namespace app\Controllers;

use \app\models\BankAccount as BankAccount;

class BankAccountController {
    protected $view;
    protected $logger;

    public function __construct(\Slim\Views\Twig $view, \Monolog\Logger $logger) {
        $this->view = $view;
        $this->logger = $logger;
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

    public function transfer($req,$res, $args) {

        $data = [];
        return $this->view->render($res, 'bankAccount/transfer.html', $data);
    }

    public function update($req, $res, $args) {
        $data = $req->getParams();
        $account = new BankAccount($data);
        $data["account"] = $account->save();

        $this::redirect_to_edit($req, $res, $data);
    }

    public function create($req, $res, $args) {
        $data = $req->getParams();
        $account = new BankAccount($data);
        $data["account"] = $account->create();

        $this::redirect_to_edit($req, $res, $data);
    }

    private function redirect_to_edit($req, $res, $data) {
        if ($data["account"] != false) {
            $data["success"] = "Your changes have been made!";
            $this->logger->addInfo($data["success"]);
        } else {
            $data["error"] = "There was an error saving the changes!";
            $this->logger->addInfo($data["error"]);
        }

        return $this->view->render($res, 'bankAccount/edit.html', $data);
    }
}
