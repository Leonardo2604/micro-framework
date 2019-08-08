<?php

namespace Core;

use stdClass;

class Route
{
    private const URL = 0;
    private const CONTROLLER_ACTION = 1;
    private const CONTROLLER = 1;
    private const ACTION = 2;
    private $routes;

    public function __construct(array $routes)
    {
        $this->setRoutes($routes);
        $this->run();
    }

    private function setRoutes(array $routes)
    {
        $newRoutes = [];
        foreach ($routes as $route) {
            $splittedAction = explode('@', $route[self::CONTROLLER_ACTION]);
            $newRoutes[] = [$route[0], $splittedAction[0], $splittedAction[1]];
        }
        $this->routes = $newRoutes;
    }

    private function getRequest(): stdClass
    {
        $obj = new stdClass();
        foreach ($_GET as $key => $value) {
            $obj->get->$key = $value;
        }

        foreach ($_POST as $key => $value) {
            $obj->post->$key = $value;
        }

        return $obj;
    }

    private function getUrl(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function run()
    {
        $url = $this->getUrl();
        $splittedUrl = explode('/', $url);
        $controller = null;
        $action = null;
        foreach ($this->routes as $route) {
            $splittedRoute = explode('/', $route[self::URL]);

            if (
                $this->matchUrlSize($splittedUrl, $splittedRoute) &&
                $url == $this->fillParameters($splittedUrl, $splittedRoute)
            ) {
                $controller = $route[self::CONTROLLER];
                $action = $route[self::ACTION];
                break;
            }
        }

        if ($controller) {
            $controller = Container::newController($controller);
            $parameters = $this->getParameters($splittedUrl, $splittedRoute);
            $parameters['request'] = $this->getRequest();
            call_user_func_array([$controller, $action], $parameters);
        } else {
            echo 'Página não encontrada!';
        }
    }

    private function matchUrlSize(array $splittedUrl, array $splittedRoute): bool
    {
        return count($splittedUrl) == count($splittedRoute);
    }

    private function getParameters(array $splittedUrl, array $splittedRoute): array
    {
        $params = [];
        for ($i = 0; $i < count($splittedRoute); $i++) {
            if ($this->isParameter($splittedRoute[$i])) {
                $params[] = $splittedUrl[$i];
            }
        }

        return $params;
    }

    private function fillParameters(array $splittedUrl, array $splittedRoute): string
    {
        for ($i = 0; $i < count($splittedRoute); $i++) {
            if ($this->isParameter($splittedRoute[$i])) {
                $splittedRoute[$i] = $splittedUrl[$i];
            }
        }

        return implode('/', $splittedRoute);
    }

    private function isParameter(string $pieceUrl): bool
    {
        return strpos($pieceUrl, '{') !== false;
    }
}
