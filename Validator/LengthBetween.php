<?php

/**
 * LengthBetween validator
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

use Luki\Validator\basicFactory;

/**
 * LengthBetween validator
 * 
 * @package Luki
 */
class LengthBetween extends basicFactory
{

    public $min = 0;
    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The length is not between "%min%" and "%max%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;
        $lenght = $this->getValueLength($value);

        if ( $lenght >= $this->min and $lenght <= $this->max ) {
            $this->setNoError();
        } else {
            $from = array( '/%min%/', '/%max%/' );
            $to = array( $this->min, $this->max );
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