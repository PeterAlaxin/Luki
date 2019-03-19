<?php
/**
 * Route class
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

namespace Luki\Router;

use Luki\Storage;

class Route
{
    private $name;
    private $pattern;
    private $modul;
    private $controller;
    private $action     = 'index';
    private $method     = array('GET');
    private $parameters = array();
    private $validator;
    private $matches    = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function setModul($modul)
    {
        $this->modul = $modul;

        return $this;
    }

    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function setMethod($method)
    {
        $this->method = explode('|', $method);

        return $this;
    }

    public function setParameters($parameters)
    {
        foreach ($parameters as $parameter => $validator) {
            $this->parameters[$parameter] = array('validator' => $validator, 'default' => null);
        }

        return $this;
    }

    public function setDefaults($defaults)
    {
        foreach ($defaults as $parameter => $value) {
            $this->parameters[$parameter]['default'] = $value;
        }

        return $this;
    }

    public function create()
    {
        $this->validator = str_replace('/', '\/', $this->pattern);

        foreach ($this->parameters as $parameter => $definition) {
            $this->validator = str_replace('{'.$parameter.'}', '('.$definition['validator'].'+)', $this->validator);
            $this->validator = str_replace('['.$parameter.']', '('.$definition['validator'].'*)', $this->validator);
        }

        $this->validator = '/^'.$this->validator.'\/*$/';
    }

    public function isCatch($url, $method)
    {

        if (in_array($method, $this->method)) {
            preg_match($this->validator, $url, $this->matches);
        }

        $isCatched = !empty($this->matches);

        if ($isCatched) {
            Storage::Set('catchedRoute', $this->name);

            if (Storage::isProfiler()) {
                $route = $this->name.' | '.$this->modul.':'.$this->controller.':'.$this->action;
                Storage::Profiler()->Add('Route', $route);
            }
        }

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
        $id        = 1;
        foreach ($this->parameters as $parameter => $options) {
            if ($this->matches[$id] !== '') {
                $arguments[] = $this->matches[$id];
            } else {
                $arguments[] = $options['default'];
            }
            $id++;
        }

        return $arguments;
    }

    public function getRoute($parameters)
    {
        $route = $this->pattern;

        foreach ($this->parameters as $parameter => $options) {
            if (array_key_exists($parameter, $parameters)) {
                $route = str_replace('{'.$parameter.'}', $parameters[$parameter], $route);
            } else {
                $route = str_replace('{'.$parameter.'}', $options['default'], $route);
            }
        }

        foreach ($parameters as $parameter => $value) {
            $route = str_replace('['.$parameter.']', $value, $route);
        }

        preg_match_all('|\[(.*)\]|U', $route, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $route = str_replace($match[0], '', $route);
        }

        return $route;
    }
}