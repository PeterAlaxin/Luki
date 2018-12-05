<?php
/**
 * Storage class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Storage
 * @filesource
 */

namespace Luki;

class Storage
{
    private static $storage = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function Set($name, $value = '', $permanent = false)
    {
        if (is_string($name)) {
            if ($permanent) {
                $_SESSION[$name] = $value;
            } else {
                self::$storage[$name] = $value;
            }
            $isSet = true;
        } else {
            $isSet = false;
        }

        return $isSet;
    }

    public static function Get($name)
    {
        if (self::isSaved($name)) {
            $value = self::$storage[$name];
        } elseif (self::isSavedPermanent($name)) {
            $value = $_SESSION[$name];
        } else {
            $value = null;
        }

        return $value;
    }

    public static function Clear($name)
    {
        if (self::isSaved($name)) {
            unset(self::$storage[$name]);
        } elseif (self::isSavedPermanent($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function isSaved($name)
    {
        $isFound = (is_string($name) and isset(self::$storage[$name]));

        return $isFound;
    }

    public static function isSavedPermanent($name)
    {
        $isFound = (is_string($name) and isset($_SESSION[$name]));

        return $isFound;
    }

    public static function __callStatic($method, $arguments)
    {
        if ('is' == substr($method, 0, 2)) {
            $variable = substr($method, 2);
            $value    = self::isSaved($variable);
        } else {
            $value = self::Get($method);
        }

        return $value;
    }

    public static function getData()
    {
        return self::$storage;
    }
}