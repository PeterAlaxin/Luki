<?php

/**
 * Loader class
 *
 * Luki framework
 * Date 18.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Loader class
 *
 * Load files, classes
 *
 * @package Luki
 */
class Loader
{

    const CLASS_NOT_EXISTS = 'Class "%s" not exists!';

    private static $_paths = array();

    protected function __construct()
    {
        
    }

    public static function Init()
    {
        self::Reset();

        spl_autoload_register('Luki\Loader::Autoload');

        $lukiDirectorys = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        array_pop($lukiDirectorys);
        
        $lukiDirectory = implode(DIRECTORY_SEPARATOR, $lukiDirectorys) . DIRECTORY_SEPARATOR;
        array_unshift(self::$_paths, $lukiDirectory);

        unset($lukiDirectorys, $lukiDirectory);
    }

    public static function Reset()
    {
        $functions = spl_autoload_functions();
        if ( is_array($functions) ) {
            foreach ( $functions as $function ) {
                spl_autoload_unregister($function);
            }
        }

        spl_autoload_register();
        self::$_paths = array();

        unset($functions, $function);
    }

    public static function addPath($path = '')
    {
        if ( !empty($path) and is_dir($path) ) {

            if ( substr($path, -1) !== DIRECTORY_SEPARATOR ) {
                $path .= DIRECTORY_SEPARATOR;
            }

            if ( !in_array($path, self::$_paths) ) {
                array_unshift(self::$_paths, $path);
            }
        }

        unset($path);
    }

    public static function getPath()
    {
        return self::$_paths;
    }

    public static function addLoader($function, $isThrow = TRUE, $isPrepend = FALSE)
    {
        if ( !empty($function) ) {
            spl_autoload_register($function, $isThrow, $isPrepend);
        }
    }

    public static function Autoload($class = '')
    {
        try {
            $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            $isFound = FALSE;

            foreach ( self::$_paths as $path ) {
                $fileWithPath = $path . $classFile;

                if ( is_file($fileWithPath) and include_once($fileWithPath) ) {
                    $isFound = TRUE;
                    break;
                }
            }

            if ( !$isFound ) {
                throw new \Exception(sprintf(self::CLASS_NOT_EXISTS, $class));
            }
        }
        catch ( \Exception $exception ) {
            exit($exception->getMessage());
        }

        unset($class, $classFile, $fileWithPath);
    }

    public static function isClass($class = '')
    {
        $className = NULL;
        $classFile = preg_replace('/_/', '/', $class) . '.php';

        foreach ( self::$_paths as $path ) {
            $fileWithPath = $path . $classFile;

            if ( is_file($fileWithPath) and is_readable($fileWithPath) ) {
                $className = $fileWithPath;
                break;
            }
        }

        unset($class, $classFile, $fileWithPath);
        return $className;
    }

    public static function isFile($file)
    {
        $fileName = NULL;

        foreach ( self::$_paths as $path ) {
            $fileWithPath = $path . $file;

            if ( is_file($fileWithPath) and is_readable($fileWithPath) ) {
                $fileName = $fileWithPath;
                break;
            }
        }

        unset($file, $fileWithPath);
        return $fileName;
    }

    private function __clone()
    {
        
    }

}

# End of file