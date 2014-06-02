<?php

/**
 * Length validator
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
 * Length validator
 * 
 * @package Luki
 */
class Length extends basicFactory
{

    public $length = NULL;

    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The length is not equal "%length%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;
        $length = $this->getValueLength($value);

        if ( $length == $this->length ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%length%/', $this->length);
        }

        unset($value);
        return $this->isValid;
    }

    public function setLength($lenght)
    {
        $this->length = (int) $lenght;

        unset($lenght);
    }

    public function getLength()
    {
        return $this->length;
    }

}

# End of file