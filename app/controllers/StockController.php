<?php
namespace app\Controllers;

use \app\models\Mysqli as Mysqli;
use \app\models\Stock as Stock;
use \app\models\StockToBuy as StockToBuy;

class StockController {
    protected $view;
    protected $logger;
    protected $user_id;

    public function __construct(\Slim\Views\Twig $view, \Monolog\Logger $logger) {
        $this->view = $view;
        $this->logger = $logger;
        $this->user_id = $_SESSION['user_id'];
    }

    public function toBuy($req, $res, $args) {
        if ($this->user_id == null) {
            return $res->withRedirect("/");
        }

        $data["available_now"] = StockToBuy::available_now();
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();
        $data["user_id"] = $this->user_id;

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
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();
        $data["user_id"] = $this->user_id;
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }

    public function cancel_buy_order($req, $res, $args) {
        $id = $req->getParam('id');
        $mysqli = Mysqli::mysqli();
        $mysqli->query("DELETE FROM stocks_to_buy WHERE id='$id'");

        $data["available_now"] = StockToBuy::available_now();
        $data["stocks"] = Stock::all_stocks();
        $data["buy_orders"] = StockToBuy::buy_orders();
        $data["user_id"] = $this->user_id;
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }
}
