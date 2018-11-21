<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App();

// Define app routes
$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello");

    return $response;
});

$app->run();
