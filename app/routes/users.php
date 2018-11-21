$app = new \Slim\App();

$app->get('/user/login/', function ($request, $response, $args) {
    return $response->write("Hi");
});
