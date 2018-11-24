<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require "../vendor/autoload.php";

$config["displayErrorDetails"] = true;
$config["addContentLengthHeader"] = false;

$app = new Slim\App([
    "settings" => $config,
    "db" => [
        "host" =>"localhost",
        "user" => "se_user",
        "password" => "se_user_password",
        "database" => "stock_exchange"
    ]
]);

$container = $app->getContainer();
$container["view"] = function($container) {
    $view = new \Slim\Views\Twig("../app/views", [
        "cache" => false
    ]);

    $router = $container->get("router");
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
};

$container[UserController::class] = function($c) {
    $view = $c->get("view");
    return new app\controllers\UserController($view);
};


//Define app routes
include "../app/routes/users.php";

$app->get("/", function (Request $request, Response $response, array $args) {
    echo phpinfo();

});

$app->run();
