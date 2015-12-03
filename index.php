<?php

include 'vendor/autoload.php';

use \Nano\Router\Router;

$router = new Router();

$router->any('/', function() {
    echo "from index";
});

$router->any('/static', function() {
    echo "from static";
});

$router->any('/dynamic/([^/]+)', function() {
    echo "from dynamic";
});

$router->get('/dynamic/<slug>/<id>', function($slug, $id) {
    echo "from dynamic with args: { slug: " . $slug . ", id: ". $id . "}";
});

$router->dispatch();
