<?php

/**
 * Loader class
 *
 * Luki framework
 * Date 18.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
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
class Loader {

    const CLASS_NOT_EXISTS = 'Class "%s" not exists!';

    /**
	 * Search path array 
	 * @access private
	 */
	private static $_aPath = array();
    
    /**
	 * Disable construct
	 */
	protected function __construct()
	{
		
	}

	/**
	 * First time initialization
	 */
	public static function Init()
	{
        self::Reset();
        
		spl_autoload_register('Luki\Loader::Autoload');

        $aLukiDirectory = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
		array_pop($aLukiDirectory);
		$sLukiDirectory = implode(DIRECTORY_SEPARATOR, $aLukiDirectory) . DIRECTORY_SEPARATOR;
		array_unshift(self::$_aPath, $sLukiDirectory);

        unset($aLukiDirectory, $sLukiDirectory);
	}

	/**
	 * Reset autolader
	 */
	public static function Reset()
	{
        $aFunctions = spl_autoload_functions();
        if(is_array($aFunctions)) {
            foreach($aFunctions as $sFunction) {
                spl_autoload_unregister($sFunction);
            }
        }
        
		spl_autoload_register();
        self::$_aPath = array();
        
        unset($aFunctions, $sFunction);
	}

    /**
	 * Initialize loader
	 * @param string $sPath
	 * @uses Luki_Loader::_Init Initialize loader
	 */
	public static function addPath($sPath = '')
	{
		if(!empty($sPath) and is_dir($sPath)) {

			if(substr($sPath, -1) !== DIRECTORY_SEPARATOR) {
				$sPath .= DIRECTORY_SEPARATOR;
			}

			if(!in_array($sPath, self::$_aPath)) {
				array_unshift(self::$_aPath, $sPath);
			}
		}

		unset($sPath);
	}

    /**
	 * Get searched path array 
	 * @return array
	 */
	public static function getPath()
	{
		return self::$_aPath;
	}
    
    /**
	 * Add to autoloader
	 */
	public static function addLoader($sFunction, $bThrow = TRUE, $bPrepend = FALSE)
	{
        if(!empty($sFunction)) {
            spl_autoload_register($sFunction, $bThrow, $bPrepend);
        }
	}

	/**
	 * Autoload function
	 * @param string $sClassName
	 * @uses Luki_Loader::_Init Initialize loader
	 */
	public static function Autoload($sClassName = '')
	{
        try {
            $sClassFile = str_replace('\\', DIRECTORY_SEPARATOR, $sClassName) . '.php';
            $bFound = FALSE;

            foreach(self::$_aPath as $sPath) {
                $sFileWithPath = $sPath . $sClassFile;
                
                if(is_file($sFileWithPath) and include_once($sFileWithPath)) {
                    $bFound = TRUE;
                    break;
                }
            }
            
            if(!$bFound) {
                throw new \Exception(sprintf(self::CLASS_NOT_EXISTS, $sClassName));
            }
        }
        catch (\Exception $oException) {
            exit($oException->getMessage());
        }
        
        unset($sClassName, $sClassFile, $sFileWithPath);
    }

    public static function isClass($sClassName = '')
	{
        var_dump($sClassName);
        
		$sReturn = NULL;
		$sClassFile = preg_replace('/_/', '/', $sClassName) . '.php';

		foreach (self::$_aPath as $sPath) {
			$sClassFileWithPath = $sPath . $sClassFile;

			if(is_file($sClassFileWithPath) and is_readable($sClassFileWithPath)) {
				$sReturn = $sClassFileWithPath;
				break;
			}
		}

		unset($sClassName, $sClassFile, $sClassFileWithPath);
		return $sReturn;
	}
    
    public static function isFile($sFileName)
	{
		$sReturn = NULL;

		foreach (self::$_aPath as $sPath) {
			$sClassFileWithPath = $sPath . $sFileName;

			if(is_file($sClassFileWithPath) and is_readable($sClassFileWithPath)) {
				$sReturn = $sClassFileWithPath;
				break;
			}
		}

		unset($sFileName, $sClassFileWithPath);
		return $sReturn;
	}
    
    /**
	 * Disable clone
	 */
	private function __clone()
	{
		
	}

}

# End of file