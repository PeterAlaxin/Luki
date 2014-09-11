<?php

/**
 * Url validator
 *
 * Luki framework
 * Date 17.12.2012
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
 * Url validator
 * 
 * @package Luki
 */
class Url extends basicFactory
{

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not valid URL!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( FALSE !== filter_var($value, FILTER_VALIDATE_URL) or 
             FALSE !== filter_var('http://' . $value, FILTER_VALIDATE_URL) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

}

# End of file