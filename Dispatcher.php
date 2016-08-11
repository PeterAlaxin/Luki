<?php
/**
 * Dispatcher class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Dispatcher
 * @filesource
 */

namespace Luki;

use Luki\Config;
use Luki\Request;

class Dispatcher
{

    private $crumb;
    private $config;
    private $isDispatched = false;
    private $crumbArray = array();
    private $controller;

    public function __construct(Request $request, Config $config)
    {
        if (Storage::Configuration()->getValue('subdomains', 'definition')) {
            $this->fixRequest($request);
        }

        $this->crumb = $request;
        $this->config = $config;
        $this->crumbArray = $request->getCrumb();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    private function fixRequest(&$request)
    {
        $serverName = $request->server->get('SERVER_NAME');
        $requestUri = $request->server->get('REQUEST_URI');
        $domain = explode('.', $serverName);

        if (count($domain) > 2) {
            $prefix = array_shift($domain);
            $request->server->set('SERVER_NAME', implode('.', $domain));
            $request->server->set('HTTP_HOST', implode('.', $domain));
            $request->server->set('REQUEST_URI', '/' . $prefix . $requestUri);
            $request->reset();
        }
    }

    public function Dispatch()
    {
        $this->isDispatched = false;
        $count = $this->crumb->getCrumbCount();
        $routes = $this->config->getSections();

        foreach ($routes as $oneRoute) {
            $route = $this->config->getSection($oneRoute);

            if ($route['count'] <= $count) {
                $this->checkRoute($route);

                if ($this->isDispatched) {
                    $this->prepareController($route);
                    $output = $this->controller->getOutput();
                    return $output;
                }
            }
        }
    }

    private function checkRoute($route)
    {
        if (is_array($route['url'])) {
            $route['url'] = '';
            $route['count'] = 0;
        }

        $url = explode('/', (string) $route['url']);
        $this->isDispatched = true;

        for ($i = 0; $i < $route['count']; $i++) {
            if ($url[$i] != $this->crumbArray[$i]) {
                $this->isDispatched = false;
                break;
            }
        }
    }

    private function prepareController($route)
    {
        $controller = $route['modul'] . '\\' . $route['controller'];
        $this->controller = new $controller;

        $methods = get_class_methods(get_class($this->controller));

        if (in_array('preDispatch', $methods)) {
            $this->controller->preDispatch();
        }

        $action = $route['action'] . 'Action';
        if (in_array($action, $methods)) {
            $this->controller->$action();
        } elseif (in_array('indexAction', $methods)) {
            $this->controller->indexAction();
        }

        if (in_array('postDispatch', $methods)) {
            $this->controller->postDispatch();
        }
    }
}
