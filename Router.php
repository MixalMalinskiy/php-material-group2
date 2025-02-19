<?php

namespace Framework;

use Cassandra\Function_;

class Router
{
    const GET = "GET";
    const POST = "POST";

    private array $routes = [
        self::GET => [],
        self::POST => [],
    ];
    private $handler404;
    private $requestUri;
    private $requestMethod;

    /**
     * @param $requestUri
     */
    public function __construct($requestUri, $requestMethod)
    {
        $this->requestUri = $requestUri;
        $this->requestMethod = $requestMethod;
    }

    public function set404Handler($handler)
    {
        $this->handler404 = $handler;
    }

    public function get($route, $handler)
    {
        $this->routes[self::GET][$route] = $handler;
    }

    public function post($route, $handler)
    {
        $this->routes[self::POST][$route] = $handler;
    }

    public function handleRequest()
    {
        $found = false;

        foreach ($this->routes[$this->requestMethod] as $route => $handler) {
            $matches = [];
            $res = preg_match("~^{$route}$~", $this->requestUri, $matches);

            if ($res === 1) {
                if (is_array($handler)) {
                    if (method_exists($handler[0], "before")) {
                        call_user_func_array([$handler[0], "before"], []);
                    }
                }

                call_user_func_array($handler, $matches);

                if (is_array($handler)) {
                    if (method_exists($handler[0], "after")) {
                        call_user_func_array([$handler[0], "after"], []);
                    }
                }
                $found = true;
            }
        }

        if (!$found) {
            call_user_func($this->handler404);
        }
    }

    public function middleware($handler)
    {
        return $this;
    }

    public function group(\Closure $param)
    {
    }


}
