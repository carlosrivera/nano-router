<?php
 
use \Nano\Router\Router;
 
class NanoRouterTest extends PHPUnit_Framework_TestCase 
{ 
    public function testMatchRoute()
    {
        $router = new Router;

        $router->any('/', function () {
        	return true;
        });

        $this->assertTrue($router->dispatch('/'));
    }

    public function testMatchIndexGet()
    {
        $router = new Router;

        $router->get('/', function () {
        	return true;
        });

        $this->assertTrue($router->dispatch('/', 'GET'));
    }

    public function testMatchIndexPost()
    {
        $router = new Router;

        $router->get('/', function () {
        	return true;
        });

        $this->assertNull($router->dispatch('/', 'POST'));
    }

    public function testMatchDynamicSlugGet()
    {
        $router = new Router;

        $router->get('/<slug>', function ($slug) {
        	$this->assertEquals($slug, 'test');
        });

        $router->dispatch('/test', 'GET');
    }
}