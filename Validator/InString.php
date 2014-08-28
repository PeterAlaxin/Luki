<?php

/**
 * InString validator
 *
 * Luki framework
 * Date 16.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * InString validator
 * 
 * @package Luki
 */
class InString extends basicFactory
{

    public $string = '';

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not in the test string!');
        
        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( 1 == preg_match('/' . (string) $value . '/i', $this->string) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

    public function setString($string)
    {
        $this->string = (string) $string;

        unset($string);
    }

    public function getString()
    {
        return $this->string;
    }

}

# End of file