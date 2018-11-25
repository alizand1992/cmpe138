<?php
namespace app\Controllers;

class BankAccountController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function index($req, $res, $args) {
        return $this->view->render($res, 'bankAccount/index.html', $args);
    }
}
