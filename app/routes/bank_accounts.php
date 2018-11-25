<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/bank_accounts', \BankAccountController::class . ':index');

$app->get('/bank_account/new', \BankAccountController::class . ':new');

$app->get('/bank_account/edit/{id}', \BankAccountController::class . ':edit');

$app->post('/bank_account/update', \BankAccountController::class . ':update');

$app->delete('/bank_account/delete/{id}', \BankAccountController::class . ':delete');

$app->post('/bank_account/create', \BankAccountController::class . ':create');
