<?php
/**
 * Security class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Security
 * @filesource
 */

namespace Luki;

class Security
{

    private static $chars = array(
        1 => '1234567890',
        2 => 'abcdefghijklmnopqrstuvwxyz',
        3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        4 => '@#$%^&*');
    private static $salt = 'DefaultSalt-ChangeMe';
    private static $algorithm = 'sha256';

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function generatePassword($lenght = 8, $level = 4)
    {
        $password = '';
        $actualLevel = 1;
        $usedLevels = array(1 => false, 2 => false, 3 => false, 4 => false);

        if ($lenght < 4) {
            $lenght = 4;
        } elseif ($lenght > 64) {
            $lenght = 64;
        }
        if (!in_array($level, array(1, 2, 3, 4))) {
            $level = 4;
        }

        while (strlen($password) < $lenght) {
            while (true) {
                $actualLevel = rand(1, $level);
                if (strlen($password) < $level) {
                    if (!$usedLevels[$actualLevel]) {
                        break;
                    }
                } else {
                    break;
                }
            }

            $usedLevels[$actualLevel] = true;
            $chars = self::$chars[$actualLevel];
            $charsLength = (strlen($chars) - 1);
            $char = $chars{rand(0, $charsLength)};

            if (0 == strlen($password) or $char != $password{strlen($password) - 1}) {
                $password .= $char;
            }
        }

        return $password;
    }

    public static function setSalt($newSalt = '')
    {
        if (empty($newSalt)) {
            $newSalt = self::generatePassword(32);
        }

        self::$salt = (string) $newSalt;
    }

    public static function getSalt()
    {
        return self::$salt;
    }

    public static function setAlgorithm($algorithm = 'sha256')
    {
        self::$algorithm = (string) $algorithm;

        unset($algorithm);
    }

    public static function getAlgorithm()
    {
        return self::$algorithm;
    }

    static function generateHash($string = '')
    {
        $hashedString = '';

        if (!empty($string)) {
            if (function_exists('hash') and in_array(self::$algorithm, hash_algos())) {
                $hashedString = hash_hmac(self::$algorithm, $string, self::$salt);
            } else {
                $hashedString = sha1(self::$salt . $string);
            }
        }

        return $hashedString;
    }
}
