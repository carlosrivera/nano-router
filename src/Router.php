<?php declare(strict_types=1);

namespace Nano\Router;

use Nano\Router\Hooks\HookManager;
use function http_response_code;

class Router
{
    private $routes;
    private $error_message;
    private $route_wildcard = "/\<([^\>]+)\>/i";
    private $route_regex = "([^/]+)";
    public $hooks;

    public function __construct()
    {
        $this->hooks = new HookManager();
    }

    public function get(string $route, callable $callback): void
    {
        $this->addRoute("GET", $route, $callback);
    }

    public function post(string $route, callable $callback): void
    {
        $this->addRoute("POST", $route, $callback);
    }

    public function any(string $route, callable $callback): void
    {
        $this->addRoute("GET|POST|PUT|DELETE|OPTIONS|HEAD", $route, $callback);
    }

    public function dispatch(string $route=null, string $method=null): bool
    {
        return $this($route, $method);
    }

    private function addRoute(string $methods, string $route, callable $callback): void
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

    private function raise404(): bool
    {
        http_response_code(404);
        echo($this->error_message);

        return false;
    }

    private function raise405(): bool
    {
        http_response_code(405);
        echo($this->error_message);

        return false;
    }

    final public function __invoke($route=null, $method=null)
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

                return $this->raise405();
            }
        }

        return $this->raise404();
    }
}
