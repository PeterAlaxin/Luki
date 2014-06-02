<?php

/**
 * Security class
 *
 * Luki framework
 * Date 7.102.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Security class
 *
 * @package Luki
 */
class Security
{

    private static $_chars = array(
      1 => '1234567890',
      2 => 'abcdefghijklmnopqrstuvwxyz',
      3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
      4 => '@#$%^&*' );
    private static $_salt = 'DefaultSalt-ChangeMe';
    private static $_algorithm = 'sha256';

    public static function generatePassword($lenght = 8, $level = 4)
    {
        $password = '';
        $actualLevel = 1;
        $usedLevels = array(
          1 => FALSE,
          2 => FALSE,
          3 => FALSE,
          4 => FALSE
        );

        if ( $lenght < 4 ) {
            $lenght = 4;
        } elseif ( $lenght > 64 ) {
            $lenght = 64;
        }
        if ( !in_array($level, array( 1, 2, 3, 4 )) ) {
            $level = 4;
        }

        while ( strlen($password) < $lenght ) {

            while ( TRUE ) {
                $actualLevel = rand(1, $level);
                if ( strlen($password) < $level ) {
                    if ( !$usedLevels[$actualLevel] ) {
                        break;
                    }
                } else {
                    break;
                }
            }

            $usedLevels[$actualLevel] = TRUE;
            $chars = self::$_chars[$actualLevel];
            $charsLength = (strlen($chars) - 1);
            $char = $chars{rand(0, $charsLength)};

            if ( 0 == strlen($password) or $char != $password{strlen($password) - 1} ) {
                $password .= $char;
            }
        }

        unset($lenght, $level, $usedLevels, $actualLevel, $chars, $charsLength, $char);
        return $password;
    }

    public static function setSalt($newSalt = '')
    {
        if ( empty($newSalt) ) {
            $newSalt = self::generatePassword(32);
        }

        self::$_salt = (string) $newSalt;

        unset($newSalt);
    }

    public static function getSalt()
    {
        return self::$_salt;
    }

    public static function setAlgorithm($algorithm = 'sha256')
    {
        self::$_algorithm = (string) $algorithm;

        unset($algorithm);
    }

    public static function getAlgorithm()
    {
        return self::$_algorithm;
    }

    static function generateHash($string = '')
    {
        $hashedString = '';

        if ( !empty($string) ) {
            if ( function_exists('hash') and in_array(self::$_algorithm, hash_algos()) ) {
                $hashedString = hash_hmac(self::$_algorithm, $string, self::$_salt);
            } else {
                $hashedString = sha1(self::$_salt . $string);
            }
        }

        unset($string);
        return $hashedString;
    }

}

# End of file