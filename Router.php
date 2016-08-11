<?php
/**
 * Router class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Router
 * @filesource
 */

namespace Luki;

use Luki\Config;
use Luki\Request;
use Luki\Router\Route;

class Router
{

    private $request;
    private $config;
    private $routes = array();
    private $controller;

    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;
        $this->config = $config;

        $this->setRoutes();
        Storage::Set('Router', $this);
    }

    public function __destruct()
    {
        foreach ($this->routes as $route) {
            $route = null;
        }

        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Dispatch()
    {
        $output = '';
        $url = '/' . $this->request->getUrl();

        foreach ($this->routes as $route) {
            if ($route->isCatch($url, $this->request->getRequestMethod())) {
                $this->prepareController($route);
                $output = $this->controller->getOutput();
                break;
            }
        }

        return $output;
    }

    public function getRoute($name, $parameters)
    {
        if (array_key_exists($name, $this->routes)) {
            $route = $this->routes[$name]->getRoute($parameters);
        } else {
            $route = '';
        }

        $route = rtrim($this->request->getShortUrl(), '/') . $route;

        return $route;
    }

    private function setRoutes()
    {
        $routes = $this->config->getSections();

        foreach ($routes as $route) {
            $item = $this->config->getSection($route);

            if ($this->isValid($item)) {
                $this->routes[$route] = $this->createRoute($route, $item);
            }
        }
    }

    private function isValid($item)
    {
        $isValid = (!empty($item['pattern']) and ! empty($item['modul']) and ! empty($item['controller']));

        return $isValid;
    }

    private function createRoute($route, $item)
    {
        $newRoute = new Route($route);
        $newRoute->setPattern($item['pattern'])
            ->setModul($item['modul'])
            ->setController($item['controller']);

        if (!empty($item['action'])) {
            $newRoute->setAction($item['action']);
        }

        if (!empty($item['method'])) {
            $newRoute->setMethod($item['method']);
        }

        if (!empty($item['parameters'])) {
            $newRoute->setParameters($item['parameters']);
        }

        if (!empty($item['defaults'])) {
            $newRoute->setDefaults($item['defaults']);
        }

        $newRoute->create();

        return $newRoute;
    }

    private function prepareController($route)
    {
        $controller = $route->getModul() . '\\' . $route->getController();
        $this->controller = new $controller;

        $methods = get_class_methods(get_class($this->controller));

        if (in_array('preDispatch', $methods)) {
            $this->controller->preDispatch();
        }

        $action = $route->getAction() . 'Action';
        $arguments = $route->getArguments();

        if (in_array($action, $methods)) {
            call_user_func_array(array($this->controller, $action), $arguments);
        } elseif (in_array('indexAction', $methods)) {
            call_user_func_array(array($this->controller, 'indexAction'), $arguments);
        }

        if (in_array('postDispatch', $methods)) {
            $this->controller->postDispatch();
        }
    }
}
