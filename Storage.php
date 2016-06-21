<?php

/**
 * Storage class
 *
 * Luki framework
 * Date 29.11.2012
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
 * Storage class
 *
 * Useful storage for any informations
 *
 * @package Luki
 */
class Storage
{

    private static $_storage = array();

    public static function Set($name, $value = '', $permanent = FALSE)
    {
        $isSet = FALSE;

        if ( is_string($name) ) {
            if($permanent) {
                $_SESSION[$name] = $value;
            }
            else {
                self::$_storage[$name] = $value;
            }
            $isSet = TRUE;
        }

        unset($name, $value, $permanent);
        return $isSet;
    }

    public static function Get($name)
    {
        $value = NULL;

        if ( self::isSaved($name) ) {
            $value = self::$_storage[$name];
        }
        elseif ( self::isSavedPermanent($name) ) {
            $value = $_SESSION[$name];
        }

        unset($name);
        return $value;
    }

    public static function Clear($name)
    {
        if ( self::isSaved($name) ) {
            unset(self::$_storage[$name]);
        }
        elseif ( self::isSavedPermanent($name) ) {
            unset($_SESSION[$name]);
        }
    }

    public static function isSaved($name)
    {
        $isFound = FALSE;

        if ( is_string($name) and isset(self::$_storage[$name]) ) {
            $isFound = TRUE;
        }

        unset($name);
        return $isFound;
    }

    public static function isSavedPermanent($name)
    {
        $isFound = FALSE;

        if ( is_string($name) and isset($_SESSION[$name]) ) {
            $isFound = TRUE;
        }

        unset($name);
        return $isFound;
    }

    public static function __callStatic($method, $arguments)
    {
        if ( 'is' == substr($method, 0, 2) ) {
            $variable = substr($method, 2);
            $value = self::isSaved($variable);
        } else {
            $value = self::Get($method);
        }

        unset($method, $arguments, $variable);
        return $value;
    }
    
    public static function getData()
    {
        return self::$_storage;
    }

}

# End of file