<?php

/**
 * LessThan validator
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
 * LessThan validator
 * 
 * @package Luki
 */
class LessThan extends basicFactory
{

    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" not less then "%max%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( $value < $this->max ) {
            $this->setNoError();
        } else {
            $from = array( '/%value%/', '/%max%/' );
            $to = array( $value, $this->max );
            $this->fillMessage($from, $to);
        }

        unset($value, $from, $to);
        return $this->isValid;
    }

    public function setMax($max)
    {
        $this->max = (float) $max;

        unset($max);
    }

    public function getMax()
    {
        return $this->max;
    }

}

# End of file