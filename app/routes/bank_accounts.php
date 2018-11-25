<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// GET USER LOGIN
$app->get('/bank_accounts', \BankAccountController::class . ':index');
