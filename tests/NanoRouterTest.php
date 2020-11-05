<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \Nano\Router\Router;

final class NanoRouterTest extends TestCase
{
    public function testMatchRoute(): void
    {
        $router = new Router;

        $router->any('/', function () {
        	return true;
        });

        $this->assertTrue($router->dispatch('/'));
    }

    public function testMatchIndexGet(): void
    {
        $router = new Router;

        $router->get('/', function () {
        	return true;
        });

        $this->assertTrue($router->dispatch('/', 'GET'));
    }

    public function testMatchIndexPost(): void
    {
        $router = new Router;

        $router->get('/', function () {
        	return true;
        });

        $this->assertFalse($router->dispatch('/', 'POST'));
    }

    public function testMatchDynamicSlugGet(): void
    {
        $router = new Router;

        $router->get('/<slug>', function ($slug) {
        	$this->assertEquals($slug, 'test');
        });

        $router->dispatch('/test', 'GET');
    }

    public function testInvokeRouter(): void
    {
        $router = new Router;


        $router->any('/', function () {
            return true;
        });

        $this->assertTrue($router('/'));
    }

    public function testBeforeRequestHook(): void
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

    public function testBeforeRequestHooks(): void
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

    public function testAfterRequestHook(): void
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
