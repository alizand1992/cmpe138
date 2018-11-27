<?php
namespace app\Controllers;

use \app\models\StockToBuy as StockToBuy;

class StockController {
    protected $view;

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function toBuy($req, $res, $args) {
        $data["available_now"] = StockToBuy::available_now();

        return $this->view->render($res, 'stock/to_buy.html', $data);
    }

    public function buy($req, $res, $args) {
        $data = $req->getParams();
        $stock = new StockToBuy($data);

        if ($stock->port_id != null) {
            $stock->buy_from_port();
        } else {
            $stock->buy();
        }

        $data["available_now"] = StockToBuy::available_now();
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }
}
