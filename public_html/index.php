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
$container["logger"] = function ($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../Logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

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
    $logger = $c->get("logger");
    return new app\controllers\UserController($view, $logger);
};

$container[BankAccountController::class] = function($c) {
    $view = $c->get("view");
    $logger = $c->get("logger");
    return new app\controllers\BankAccountController($view, $logger);
};

$container[StockController::class] = function($c) {
    $view = $c->get("view");
    $logger = $c->get("logger");
    return new app\controllers\StockController($view, $logger);
};

//Define app routes
include "../app/routes/users.php";
include "../app/routes/bank_accounts.php";
include "../app/routes/stocks.php";

$app->get("/", function (Request $request, Response $response, array $args) {
    if ($_SESSION["user_id"]) {
        return $response->withRedirect("/user/profile");
    } else {
        return $response->withRedirect("/user/login");
    }
});

$app->run();
