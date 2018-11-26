<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/stocks', \StockController::class . ':index');

$app->get('/stocks/to_buy', \StockController::class . ':toBuy');

$app->get('/stocks/to_sell', \StockController::class . ':toSell');

$app->post('/stocks/buy', \StockController::class . ':buy');

