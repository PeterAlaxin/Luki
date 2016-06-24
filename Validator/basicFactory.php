<?php

/**
 * Validator factory
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\basicInterface;

/**
 * Validator factory
 * 
 * @abstract
 * @package Luki
 */
abstract class basicFactory implements basicInterface
{

    private $_error = '';
    private $_validator = '';
    private $_message = '';
    
    public $isValid;
    
    const ALPHA = 'a-zA-ZáäčďéěëíľňôóöŕřšťúůüýžÁÄČĎÉĚËÍĽŇÓÖÔŘŔŠŤÚŮÜÝŽ\ ';
    const NUM = '0-9';
    const CHARS = '\r\n\+\-\*\\\.\,\:\;\%\(\)\/\?\!\&\=\_\@\#\$\^\{\}\"\'\|\`\<\>\~';
    
    public function __construct($options = array())
    {
        foreach ( $options as $key => $value ) {
            $this->$key = $value;
        }

        unset($options, $key, $value);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( 1 === preg_match($this->_validator, $value) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

    public function setMessage($message)
    {
        $this->_message = $message;
    }

    public function getMessage()
    {
        return $this->_message;
    }
    
    public function fillMessage($from, $to)
    {
        $this->_error = preg_replace($from, $to, $this->_message);
        
        unset($from, $to);
    }
    
    public function setValidator($validator)
    {
        $this->_validator = $validator;
    }

    public function getValidator()
    {
        return $this->_validator;
    }

    public function setError($error)
    {
        $this->_error = $error;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function setNoError()
    {
        $this->_error = '';
        $this->isValid = TRUE;
    }
    
    public function getValueLength($value)
    {
        $length = NULL;

        if ( is_string($value) ) {
            $length = strlen($value);
        } elseif ( is_array($value) ) {
            $length = count($value);
        }

        unset($value);
        return $length;
    }

}

# End of file