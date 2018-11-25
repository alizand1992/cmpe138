<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/stocks', \BankAccountController::class . ':index');

$app->get('/stocks/to_buy', \BankAccountController::class . ':toBuy');
