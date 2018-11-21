<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

$app->get('/user/login', function (Request $request, Response $response) {
    $response = $this->view->render($response, 'user/login.php', []);
    return $response;
});
