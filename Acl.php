<?php

/**
 * Acl class
 *
 * Luki framework
 * Date 24.9.2012
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

namespace Luki;

/**
 * Acl class
 *
 * Access control list
 *
 * @package Luki
 */
class Acl
{

    private $_rules = array();

    public function allow($controler, $action)
    {
        $this->_rules[$controler][$action] = TRUE;
    }
    
    public function deny($controler, $action)
    {
        $this->_rules[$controler][$action] = FALSE;        
    }
    
    public function isAllowed($controler, $action)
    {
        if(!empty($this->_rules[$controler]) and 
           !empty($this->_rules[$controler][$action]) and 
           $this->_rules[$controler][$action] === TRUE) {
            $allowed = TRUE;
        }
        else {
            $allowed = FALSE;
        }
        
        return $allowed;
    }
    
    public function getData()
    {
        return $this->_rules;
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