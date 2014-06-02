<?php

/**
 * GreaterThan validator
 *
 * Luki framework
 * Date 14.12.2012
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
 * GreaterThan validator
 * 
 * @package Luki
 */
class GreaterThan extends basicFactory
{

    public $min = 0;

    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" not greater then "%min%"!');
        
        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( $value > $this->min ) {
            $this->setNoError();
        } else {
            $from = array( '/%value%/', '/%min%/' );
            $to = array( $value, $this->min );
            $this->fillMessage($from, $to);
        }

        unset($value, $from, $to);
        return $this->isValid;
    }

    public function setMin($min)
    {
        $this->min = (float) $min;

        unset($min);
    }

    public function getMin()
    {
        return $this->min;
    }

}

# End of file