<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require '../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new Slim\App(['settings' => $config]);


$container = $app->getContainer();
$container['view'] = new PhpRenderer("../app/views/");

//Define app routes

include '../app/routes/users.php';

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello");

    $response = $this->view->render($response, '/user/login.php', ['test' => 'test']);

    return $response;
});

$app->run();
