<?php

/**
 * Date validator
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
 * Date validator
 * 
 * @package Luki
 */
class Date extends basicFactory
{

    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" is not valid date!');
        
        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( FALSE !== date_create($value) ) {
            $this->setNoError();
        } else {
            $this->fillMessage('/%value%/', $value);
        }

        unset($value);
        return $this->isValid;
    }

}

# End of file