<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

// GET USER LOGIN
$app->get('/user/login', function (Request $request, Response $response) {
    $response = $this->view->render($response, 'user/login.html', []);
    return $response;
});


// GET USER REGISTRATION
$app->get('/user/register', function (Request $request, Response $response) {
    $response = $this->view->render($response, 'user/register.html', []);
    return $response;
});
