<?php

/**
 * LengthMax validator
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
 * LengthMax validator
 * 
 * @package Luki
 */
class LengthMax extends basicFactory
{

    public $max = 0;
    
    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The length is greater then "%max%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;
        $nLength = $this->getValueLength($value);

        if ( $nLength <= $this->max ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%max%/', $this->max);
        }

        unset($value);
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