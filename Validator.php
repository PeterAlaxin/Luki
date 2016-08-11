<?php
/**
 * Validator class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

namespace Luki;

class Validator
{

    private static $error = '';

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function isValid($value, $validatorName, $options = array(), $message = '')
    {
        $validator = new $validatorName($options);
        self::$error = '';

        if (!empty($message)) {
            $validator->setMessage($message);
        }

        $isValid = $validator->isValid($value);

        if (!$isValid) {
            self::$error = $validator->getError();
        }

        return $isValid;
    }

    public static function getError()
    {
        return self::$error;
    }
}
