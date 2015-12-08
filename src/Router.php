<?php
namespace Nano\Router;

use Nano\Router\Hooks\HookManager;

class Router 
{
    private $route_wildcard = "/\<([^\>]+)\>/i";
    private $route_regex = "([^/]+)";
    public $hooks;

    public function __construct()
    {
        $this->hooks = new HookManager();
    } 

    function get($route, callable $callback) 
    {
        $this->addRoute("GET", $route, $callback);
    }

    function post($route, callable $callback) 
    {
        $this->addRoute("POST", $route, $callback);
    }

    function any($route, callable $callback) 
    {
        $this->addRoute("GET|POST|PUT|DELETE|OPTIONS|HEAD", $route, $callback);
    }

    function dispatch($route=null, $method=null) 
    { 
        return $this($route, $method);
    }

    private function addRoute($methods, $route, callable $callback) 
    {
        $regex = preg_replace($this->route_wildcard, $this->route_regex, strtolower($route));
    
        /*if (preg_match_all($this->route_wildcard, $route, $matches)) {
            foreach ($matches[0] as $key => $value) {
                $regex = str_replace($value, "([^/]+)", $regex);
            }
            $params = $matches[1];
        }*/

        $this->routes[$regex] = [
            'methods'   => $methods,
            'route'     => $route,
            'callback'  => $callback,
        ];
    }

    private function rise404() 
    {
        echo "404 - Not found";
        http_response_code(404);
    }

    private function rise405() 
    {
        echo "405 - Not allowed";
        http_response_code(405);
    }

    public function __invoke($route=null, $method=null)
    {
        $path = ($route != null) ? $route : (isset($_SERVER['PATH_INFO']) ? strtolower($_SERVER['PATH_INFO']) : '/');
        $method = ($method != null) ? $method : (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');

        foreach ($this->routes as $key => $value) {
            if (preg_match("~^(?:". $key . ")$~x", $path, $matches)) {

                if (preg_match("/".$method."/", $value['methods'])) {

                    foreach ($this->hooks->beforeRequest->all() as $hook_key => $hook) {
                        call_user_func_array($hook, []);
                    }

                    call_user_func_array($this->routes[$key]['callback'], array_slice($matches, 1));

                    foreach ($this->hooks->afterRequest->all() as $hook_key => $hook) {
                        call_user_func_array($hook, []);
                    }

                    return true;
                }
                
                return $this->rise405();
            }
        }

        return $this->rise404();
    }
}