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

    public function testInvokeRouter()
    {
        $router = new Router;


        $router->any('/', function () {
            return true;
        });

        $this->assertTrue($router('/'));
    }

    public function testBeforeRequestHook()
    {
        $router = new Router;

        $closures = 0;

        $router->any('/', function () use (&$closures) {
            $this->assertEquals($closures, 1);
        });

        $router->hooks->beforeRequest->add(function() use (&$closures) {
            $closures++;
        });

        $router('/');
    }

    public function testBeforeRequestHooks()
    {
        $router = new Router;

        $closures = 0;
        $target = 10;

        $router->any('/', function () use (&$closures, $target) {
            $this->assertEquals($closures, $target);
        });

        for ($i=0; $i < $target; $i++) { 
            $router->hooks->beforeRequest->add(function() use (&$closures) {
                $closures++;
            });
        }
        
        $router('/');
    }

    public function testAfterRequestHook()
    {
        $router = new Router;

        $closures = 0;

        $router->any('/', function () use (&$closures) {
            $closures++;
        });

        $router->hooks->afterRequest->add(function() use (&$closures) {
            $this->assertEquals($closures, 1);
        });

        $router('/');
    }

}