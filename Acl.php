<?php
/**
 * Acl class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Acl
 * @filesource
 */

namespace Luki;

class Acl
{
    private $rules = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function allow($controler, $action)
    {
        $this->rules[$controler][$action] = true;
    }

    public function deny($controler, $action)
    {
        $this->rules[$controler][$action] = false;
    }

    public function isAllowed($controler, $action)
    {
        if (!empty($this->rules[$controler]) and ! empty($this->rules[$controler][$action]) and
            $this->rules[$controler][$action] === true) {
            $allowed = true;
        } else {
            $allowed = false;
        }

        return $allowed;
    }

    public function getData()
    {
        return $this->rules;
    }

    public function __call($name, $arguments)
    {
        list($controler, $action) = explode('_', $name);
        $allowed = $this->isAllowed($controler, $action);

        return $allowed;
    }

    public function __get($name)
    {
        list($controler, $action) = explode('_', $name);
        $allowed = $this->isAllowed($controler, $action);

        return $allowed;
    }
}