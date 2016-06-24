<?php

/**
 * Validator class
 *
 * Luki framework
 * Date 16.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Validation aggregation class
 *
 * @package Luki
 */
class Validator
{

    private static $_error = '';

    public static function isValid($value, $validatorName, $options = array(), $message = '')
    {
        $validator = new $validatorName($options);
        self::$_error = '';

        if ( !empty($message) ) {
            $validator->setMessage($message);
        }

        $isValid = $validator->isValid($value);

        if ( !$isValid ) {
            self::$_error = $validator->getError();
        }

        unset($value, $validatorName, $options, $validator, $message);
        return $isValid;
    }

    public static function getError()
    {
        return self::$_error;
    }

}

# End of file