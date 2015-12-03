<?php
namespace Nano\Router;

class Router 
{
    private $route_wildcard = "/\<([^\>]+)\>/i";
    private $route_regex = "([^/]+)";

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

    function dispatch() 
    { 
        $path = isset($_SERVER['PATH_INFO']) ? strtolower($_SERVER['PATH_INFO']) : '/';

        foreach ($this->routes as $key => $value) {
            if (preg_match("~^(?:". $key . ")$~x", $path, $matches)) {

                if (preg_match("/".$_SERVER['REQUEST_METHOD']."/", $value['methods'])) {
                    call_user_func_array($this->routes[$key]['callback'], array_slice($matches, 1));

                    return true;
                }
                
                return $this->rise405();
            }
        }

        return $this->rise404();
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
}