<?php

/**
 * Blank validator
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
 * Blank validator
 * 
 * @package Luki
 */
class Blank extends basicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not blank!');
        
        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( empty($value) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

}

# End of file