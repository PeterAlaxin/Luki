<?php
/**
 * Loader class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Loader
 * @filesource
 */

namespace Luki;

use Luki\Exception\LoaderException;

class Loader
{

    const CLASS_NOT_EXISTS = 'Class "%s" not exists!';

    private static $paths = array();

    protected function __construct()
    {
        
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function Init()
    {
        self::Reset();

        spl_autoload_register('Luki\Loader::Autoload');

        $lukiDirectorys = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        array_pop($lukiDirectorys);

        $lukiDirectory = implode(DIRECTORY_SEPARATOR, $lukiDirectorys) . DIRECTORY_SEPARATOR;
        array_unshift(self::$paths, $lukiDirectory);
    }

    public static function Reset()
    {
        $functions = spl_autoload_functions();
        if (is_array($functions)) {
            foreach ($functions as $function) {
                spl_autoload_unregister($function);
            }
        }

        spl_autoload_register();
        self::$paths = array();
    }

    public static function addPath($path = '')
    {
        if (!empty($path) and is_dir($path)) {

            if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
                $path .= DIRECTORY_SEPARATOR;
            }

            if (!in_array($path, self::$paths)) {
                array_unshift(self::$paths, $path);
            }
        }
    }

    public static function getPath()
    {
        return self::$paths;
    }

    public static function addLoader($function, $isThrow = true, $isPrepend = false)
    {
        if (!empty($function)) {
            spl_autoload_register($function, $isThrow, $isPrepend);
        }
    }

    public static function Autoload($class = '')
    {
        try {
            $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            $isFound = false;

            foreach (self::$paths as $path) {
                $fileWithPath = $path . $classFile;

                if (is_file($fileWithPath) and include_once($fileWithPath)) {
                    $isFound = true;
                    break;
                }
            }

            if (!$isFound) {
                throw new LoaderException(sprintf(self::CLASS_NOT_EXISTS, $class));
            }
        } catch (\Exception $exception) {
            throw new LoaderException($exception->getMessage());
        }
    }

    public static function isClass($class = '')
    {
        $className = null;
        $classFile = preg_replace('/_/', '/', $class) . '.php';

        foreach (self::$paths as $path) {
            $fileWithPath = $path . $classFile;

            if (is_file($fileWithPath) and is_readable($fileWithPath)) {
                $className = $fileWithPath;
                break;
            }
        }

        return $className;
    }

    public static function isFile($file)
    {
        $fileName = null;

        foreach (self::$paths as $path) {
            $fileWithPath = $path . $file;

            if (is_file($fileWithPath) and is_readable($fileWithPath)) {
                $fileName = $fileWithPath;
                break;
            }
        }

        return $fileName;
    }

    private function __clone()
    {
        
    }
}
