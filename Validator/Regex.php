<?php

/**
 * Regex validator
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
 * Regex validator
 * 
 * @package Luki
 */
class Regex extends basicFactory
{

    public $regex = NULL;

    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" does not match regular expression "%regex%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( 1 === preg_match($this->regex, $value) ) {
            $this->setNoError();
        } else {
            $from = array( '/%value%/', '/%regex%/' );
            $to = array( $value, $this->regex );
            $this->fillMessage($from, $to);
        }

        unset($value, $from, $to);
        return $this->isValid;
    }

    public function setRegex($regex)
    {
        $this->regex = (float) $regex;

        unset($regex);
    }

    public function getRegex()
    {
        return $this->regex;
    }

}

# End of file