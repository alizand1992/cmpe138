<?php
require 'vendor/autoload.php';

$app = new \Slim\App();

// Define app routes
$app->get('/', function ($request, $response, $args) {
    echo "Hello World!";
});

$app->run();
