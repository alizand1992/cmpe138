<?php
namespace app\Controllers;

use \app\models\Mysqli as Mysqli;
use \app\models\Stock as Stock;
use \app\models\StockToBuy as StockToBuy;

class StockController {
    protected $view;
    protected $logger;

    public function __construct(\Slim\Views\Twig $view, \Monolog\Logger $logger) {
        $this->view = $view;
        $this->logger = $logger;
    }

    public function toBuy($req, $res, $args) {
        $data["available_now"] = StockToBuy::available_now();
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();

        return $this->view->render($res, 'stock/to_buy.html', $data);
    }

    public function buy($req, $res, $args) {
        $data = $req->getParams();
        $stock = new StockToBuy($data);

        if ($stock->port_id != null) {
            $result = $stock->buy_from_port();

            if ($result != null) {
                $data["error"] = $result;
                $this->logger->addInfo($result);
            }
        } else {
            $result = $stock->buy();

            if ($result != null) {
                $data["error"] = $result;
                $this->logger->addInfo($result);
            }
        }

        $data["available_now"] = StockToBuy::available_now();
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }

    public function cancel_buy_order($req, $res, $args) {
        $id = $req->getParam('id');
        $mysqli = Mysqli::mysqli();
        $mysqli->query("DELETE FROM stocks_to_buy WHERE id='$id'");

        $data["available_now"] = StockToBuy::available_now();
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }
}
