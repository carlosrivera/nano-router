<?php declare(strict_types=1);

namespace Nano\Router\Hooks;

class HookManager
{
    private $data = [];
    public $beforeRequest;
    public $afterRequest;

    public function &__get($key)
    {
        return $this->data[$key];
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    public function __construct()
    {
        $this->beforeRequest = new HookList();
        $this->afterRequest = new HookList();
    }
}
