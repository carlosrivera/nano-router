<?php

include 'vendor/autoload.php';

use \Nano\Router\Router;

$router = new Router();

$router->any('/', function() {
    echo "from index \n";
});

$router->any('/static', function() {
    echo "from static \n";
});

$router->any('/dynamic/([^/]+)', function() {
    echo "from dynamic \n";
});

$router->get('/dynamic/<slug>/<id>', function($slug, $id) {
    echo "from dynamic with args: { slug: " . $slug . ", id: ". $id . "} \n";
});

$router->hooks->beforeRequest->add(function() {
	echo "before request firts hook \n";
});

$router->hooks->beforeRequest->add(function() {
	echo "before request second hook \n";
});

$router->hooks->afterRequest->add(function() {
	echo "after request hook \n";
});

$router();
