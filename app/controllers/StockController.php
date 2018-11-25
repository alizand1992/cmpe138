<?php
namespace app\Controllers;

class StockController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function toBuy($req, $res, $args) {

        return $this->view->render($res, 'stock/to_buy.html', $args);
    }
}
