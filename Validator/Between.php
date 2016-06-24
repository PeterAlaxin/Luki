<?php

/**
 * Between validator
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
 * Between validator
 * 
 * @package Luki
 */
class Between extends basicFactory
{

    public $min = 0;
    public $max = 0;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not between "%min%" and "%max%"!');
        
        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( (float) $value >= $this->min and (float) $value <= $this->max ) {
            $this->setNoError();
        } else {
            $from = array( '/%value%/', '/%min%/', '/%max%/' );
            $to = array( $value, $this->min, $this->max );
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