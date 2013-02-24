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

/**
 * Storage class
 *
 * Useful storage for any informations
 *
 * @package Luki
 */
class Luki_Storage {

	/**
	 * Flag for installed storage
	 *
	 * @access private
	 */
	private static $_storage = array();

	/**
	 * Set data to storage
	 *
	 * @param string $sName Variable name
	 * @param mixed $xValue Variable value
	 * @uses Storage::_Init() Initialization storage
	 */
	public static function Set($sName, $xValue = '')
	{
		$bReturn = FALSE;

		if(is_string($sName)) {
			self::$_storage[$sName] = $xValue;
			$bReturn = TRUE;
		}

		unset($sName, $xValue);
		return $bReturn;
	}

	/**
	 * Get data from storage
	 *
	 * @param string $sName Variable name
	 * @return mixed Variable value
	 * @uses Storage::isSaved() Check if variable defined
	 */
	public static function Get($sName)
	{
		$xReturn = NULL;

		if(self::isSaved($sName)) {
			$xReturn = self::$_storage[$sName];
		}

		unset($sName);
		return $xReturn;
	}

	/**
	 * Check if variable saved
	 *
	 * @param string $sName Variable name
	 */
	public static function isSaved($sName)
	{
		$bReturn = FALSE;

		if(is_string($sName) and isset(self::$_storage[$sName])) {
			$bReturn = TRUE;
		}

		unset($sName);
		return $bReturn;
	}

}

# End of file