<?php
namespace app\Controllers;

use \app\models\StockToBuy as StockToBuy;

class StockControllerHelper {
    static function logger($filename, $data) {
        $current = file_get_contents($filename);
        $current .= $data . "\n";
        file_put_contents($filename, $current);
    }
}

class StockController {
    protected $view;
    $fname = '../logfile.txt';

    public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    }

    public function toBuy($req, $res, $args) {
        $data["available_now"] = StockToBuy::available_now();
        StockControllerHelper::logger($fname, $data["available_now"]);
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }

    public function buy($req, $res, $args) {
        $data = $req->getParams();
        $stock = new StockToBuy($data);
        $stock->buy();

        $data["available_now"] = StockToBuy::available_now();
        StockControllerHelper::logger($fname, $data["available_now"]);
        return $this->view->render($res, 'stock/to_buy.html', $data);
    }
}
