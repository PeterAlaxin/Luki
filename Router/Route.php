<?php

/**
 * Route class
 *
 * Luki framework
 * Date 7.4.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Router;

use Luki\Storage;

/**
 * Route class
 *
 * @package Luki
 */
class Route
{
    private $name;
    private $pattern;
    private $modul;
    private $controller;
    private $action = 'index';
    private $method = array('GET');
    private $parameters = array();
    private $validator;
    private $matches = array();
    
    public function __construct($name)
    {
        $this->name = $name;
        
        unset($name);
    }
    
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        
        unset($pattern);
        return $this;
    }

    public function setModul($modul)
    {
        $this->modul = $modul;
        
        unset($modul);
        return $this;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        
        unset($controller);
        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;
        
        unset($action);
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = explode('|', $method);
        
        unset($method);
        return $this;
    }
    
    public function setParameters($parameters)
    {
        foreach($parameters as $parameter => $validator) {
            $this->parameters[$parameter] = array('validator' => $validator, 'default' => NULL);
        }        
        
        unset($parameters, $parameter, $validator);
        return $this;
    }

    public function setDefaults($defaults)
    {
        foreach($defaults as $parameter => $value) {            
            $this->parameters[$parameter]['default'] = $value;
        }        
        
        unset($defaults, $parameter, $value);
        return $this;
    }
    
    public function create()
    {
        $this->validator = str_replace('/', '\/*', $this->pattern);
        
        foreach($this->parameters as $parameter => $definition) {
            $this->validator = str_replace('{' . $parameter . '}', '(' . $definition['validator'] . '+)', $this->validator);
            $this->validator = str_replace('[' . $parameter . ']', '(' . $definition['validator'] . '*)', $this->validator);
        }
        
        $this->validator = '/^' . $this->validator . '\/*$/';
    }
    
    public function isCatch($url, $method)
    {        
        if( in_array($method, $this->method)) {
            preg_match($this->validator, $url, $this->matches);        
        }
        
        $isCatched = !empty($this->matches);
        
        if ( $isCatched and Storage::isProfiler() ) {
            $route = $this->name . ' | ' . $this->modul . ':' . $this->controller . ':' . $this->action;
            Storage::Profiler()->Add('Route', $route);
        }

        unset($url, $method, $route);
        return $isCatched;            
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getModul()
    {
        return $this->modul;
    }

    public function getAction()
    {
        return $this->action;
    }
    
    public function getArguments()
    {
        $arguments = array();
        $id = 1;
        foreach($this->parameters as $parameter => $options) {
            if($this->matches[$id] !== '') {
                $arguments[] = $this->matches[$id];
            }
            else { 
                $arguments[] = $options['default'];                
            }
            $id++;
        }
        
        return $arguments;
    }
    
    public function getRoute($parameters)
    {
        $route = $this->pattern;
        
        foreach($this->parameters as $parameter => $options) {
            if($parameters[$parameter] !== '') {
                $route = str_replace('{' . $parameter . '}', $parameters[$parameter], $route);
            }
            else {
                $route = str_replace('{' . $parameter . '}', $options['default'], $route);
            }
        }
        
        foreach($parameters as $parameter => $value) {
            $route = str_replace('[' . $parameter . ']', $value, $route);
        }
        
        preg_match_all('|\[(.*)\]|U', $route, $matches, PREG_SET_ORDER);
        foreach($matches as $match) {
            $route = str_replace($match[0], '', $route);            
        }
        
        unset($parameters, $parameter, $options, $value);
        return $route;
    }
}