<?php
namespace app\Controllers;

class StockController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function toBuy($req, $res, $args) {

    }
}
