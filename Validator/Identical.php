<?php

/**
 * Identical validator
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
 * Identical validator
 * 
 * @package Luki
 */
class Identical extends basicFactory
{

    public $token = NULL;

    public function __construct($options)
    {
        parent::__construct($options);

        $this->setMessage('The value "%value%" not identical as "%token%"!');

        unset($options);
    }

    public function isValid($value)
    {
        $this->isValid = FALSE;

        if ( $value === $this->token ) {
            $this->setNoError();
        } else {
            $from = array( '/%value%/', '/%token%/' );
            $to = array( $value, $this->token );
            $this->fillMessage($from, $to);
        }

        unset($value, $from, $to);
        return $this->isValid;
    }

    public function setToken($token)
    {
        $this->token = $token;

        unset($token);
    }

    public function getToken()
    {
        return $this->token;
    }

}

# End of file