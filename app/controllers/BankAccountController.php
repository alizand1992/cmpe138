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
        return $this->view->render($res, 'bankAccount/edit.html', $args);
    }

    public function update($req, $res, $args) {

        // return $this->view->render($res, 'bankAccount/edit.html', $args);
    }
}
