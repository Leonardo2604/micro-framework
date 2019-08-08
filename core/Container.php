<?php

namespace Core;

class Container
{
    public static function newController(string $controller)
    {
        $controller = "App\\Controllers\\{$controller}";
        return new $controller;
    }
}
