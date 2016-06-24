<?php

/**
 * IP validator
 *
 * Luki framework
 * Date 16.12.2012
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
 * IP validator
 * 
 * @package Luki
 */
class IP extends basicFactory
{
    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not valid IPv4 or IPv6 address!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( FALSE !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) or
                FALSE !== filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

}

# End of file