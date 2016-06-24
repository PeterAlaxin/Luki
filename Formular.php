<?php

/**
 * Formular class
 *
 * Luki framework
 * Date 19.9.2012
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
 * Formular class
 *
 * Formular management
 *
 * @package Luki
 */
class Formular
{

    private $_name = '';
    private $_id = '';
    private $_method = 'post';
    private $_inputs = array();
    private $_enctype = 'application/x-www-form-urlencoded';
    private $_hasFocus = TRUE;
    private $_action = '';
    private $_errors = array();
    private $_autocomplete = 'on';
    private $_novalidate = FALSE;
    private $_target = '_self';
    private $_class = '';

    public function __construct($name)
    {
        $this->_name = $name;
        $this->_id = $name . '_id';

        unset($name);
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setMethod($method)
    {
        if ( in_array($method, array( 'post', 'get' )) ) {
            $this->_method = $method;
        }

        unset($method);
        return $this;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setEnctype($enctype)
    {
        if ( in_array($enctype, array( 'application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain' )) ) {
            $this->_enctype = $enctype;
        }

        unset($enctype);
        return $this;
    }

    public function getEnctype()
    {
        return $this->_enctype;
    }

    public function setFocus($focus)
    {
        $this->_hasFocus = (bool) $focus;

        unset($focus);
        return $this;
    }

    public function getFocus()
    {
        return $this->_hasFocus;
    }

    public function setAction($action)
    {
        $this->_action = (string) $action;

        unset($action);
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setAutocomplete($autocomplete)
    {
        if ( in_array($autocomplete, array( 'on', 'off' )) ) {
            $this->_autocomplete = $autocomplete;
        }

        unset($autocomplete);
        return $this;
    }

    public function getAutocomplete()
    {
        return $this->_autocomplete;
    }

    public function setNovalidate()
    {
        $this->_novalidate = TRUE;
        return $this;
    }

    public function getNovalidate()
    {
        return $this->_novalidate;
    }

    public function setTarget($target)
    {
        if ( in_array($target, array( '_blank', '_self', '_parent', '_top' )) ) {
            $this->_target = $target;
        }

        unset($target);
        return $this;
    }

    public function getTarget()
    {
        return $this->_target;
    }

    public function setClass($class)
    {
        $this->_class = $class;

        unset($class);
        return $this;
    }

    public function getClass()
    {
        return $this->_class;
    }

    public function addInput($input)
    {
        $this->_inputs[$input->getName()] = $input;

        unset($input);
        return $this;
    }

    public function getInputs()
    {
        $inputs = array();

        foreach ( $this->_inputs as $name => $input ) {
            $inputs[$name] = $input->getHtml();
        }

        unset($name, $input);
        return $inputs;
    }

    public function fillValues($values)
    {
        foreach ( $values as $name => $value ) {
            if ( array_key_exists($name, $this->_inputs) ) {
                $this->_inputs[$name]->setValue($value);
            }
        }

        unset($values, $name, $value);
        return $this;
    }

    public function isValid()
    {
        $valid = TRUE;

        foreach ( $this->_inputs as $name => $input ) {
            if ( !$input->isValid() ) {
                $this->_errors[$name] = $input->getErrors();
                $valid = FALSE;
            }
        }

        unset($name, $input);
        return $valid;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getFormular()
    {
        $formular = array( 'form' => $this->getFormularHeader(),
          'inputs' => $this->getInputs(),
          'errors' => $this->getErrors() );

        return $formular;
    }

    public function getFormularHeader()
    {
        $header = '<form ';
        $header .= 'action="' . $this->getAction() . '" ';
        $header .= 'autocomplete="' . $this->getAutocomplete() . '" ';
        $header .= 'enctype="' . $this->getEnctype() . '" ';
        $header .= 'id="' . $this->getId() . '" ';
        $header .= 'method="' . $this->getMethod() . '" ';
        $header .= 'name="' . $this->getName() . '" ';
        $header .= 'target="' . $this->getTarget() . '" ';
        $header .= 'class="' . $this->getClass() . '" ';

        if ( $this->getNovalidate() ) {
            $header .= 'novalidate ';
        }

        $header .= '>';

        return $header;
    }

    public function getResult()
    {
        if ( empty($this->_errors) ) {
            $return = array( 'status' => 0 );
        } else {
            $return = array( 'status' => 1 );
            foreach ( $this->getErrors() as $field => $errors ) {
                $return['fields'][] = $field;
                $return['errors'][$field] = $errors;
            }
        }

        unset($field, $errors);
        return $return;
    }

}

# End of file