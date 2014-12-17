<?php

/**
 * Dispatcher class
 *
 * Luki framework
 * Date 7.7.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Config;
use Luki\Request;

/**
 * Dispatcher class
 *
 * @package Luki
 */
class Dispatcher
{

    private $_crumb;
    private $_config;
    private $_isDispatched = FALSE;
    private $_crumbArray = array();
    private $_controller;

    public function __construct(Request $request, Config $config)
    {
        $this->_fixRequest($request);
        
        $this->_crumb = $request;
        $this->_config = $config;
        $this->_crumbArray = $request->getCrumb();

        unset($config, $request);
    }

    private function _fixRequest(&$request) 
    {
        $serverName = $request->server->get('SERVER_NAME');
        $requestUri =  $request->server->get('REQUEST_URI');
        $domain = explode('.', $serverName);
        
        if(count($domain) > 2) {
            $prefix = array_shift($domain);
            $request->server->set('SERVER_NAME', implode('.', $domain));
            $request->server->set('HTTP_HOST', implode('.', $domain));
            $request->server->set('REQUEST_URI', '/' . $prefix . $requestUri);
            $request->reset();
        }
        
        unset($serverName, $requestUri, $domain, $prefix);
    }
    
    public function Dispatch()
    {
        $this->_isDispatched = FALSE;
        $count = $this->_crumb->getCrumbCount();
        $routes = $this->_config->getSections();

        foreach ($routes as $oneRoute) {
            $route = $this->_config->getSection($oneRoute);

            if ($route['count'] <= $count) {
                $this->_checkRoute($route);

                if ($this->_isDispatched) {
                    $this->_prepareController($route);
                    $output = $this->_controller->getOutput();
                    return $output;
                }
            }
        }
    }

    private function _checkRoute($route)
    {
        if (is_array($route['url'])) {
            $route['url'] = '';
            $route['count'] = 0;
        }
        
        $url = explode('/', (string) $route['url']);
        $isEqual = TRUE;

        for ($i = 0; $i < $route['count']; $i++) {
            if ($url[$i] != $this->_crumbArray[$i]) {
                $isEqual = FALSE;
                break;
            }
        }

        $this->_isDispatched = $isEqual;

        unset($route, $url, $isEqual, $i);
    }

    private function _prepareController($route)
    {
        $controller = $route['modul'] . '\\' . $route['controller'];
        $this->_controller = new $controller;

        $methods = get_class_methods(get_class($this->_controller));

        if (in_array('preDispatch', $methods)) {
            $this->_controller->preDispatch();
        }

        $action = $route['action'] . 'Action';
        if (in_array($action, $methods)) {
            $this->_controller->$action();
        } elseif (in_array('indexAction', $methods)) {
            $this->_controller->indexAction();
        }

        if (in_array('postDispatch', $methods)) {
            $this->_controller->postDispatch();
        }

        unset($route, $controller, $methods, $action);
    }

}
