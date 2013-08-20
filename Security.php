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
class Security {

	private static $aChars = array(
		1 => '1234567890',
		2 => 'abcdefghijklmnopqrstuvwxyz',
		3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		4 => '@#$%^&*');
	private static $sSalt = 'DefaultSalt-ChangeMe';
	private static $sAlgorithm = 'sha256';

	/**
	 * Generate secure password
	 *
	 * @param integer $nLenght Password lenght
	 * @param integer $nLevel Password level
	 * @return string New password
	 */
	public static function generatePassword($nLenght = 8, $nLevel = 4)
	{
		$sReturn = '';
		$nLevelNow = 1;
		$aUsedLevels = array(
			1 => FALSE,
			2 => FALSE,
			3 => FALSE,
			4 => FALSE
		);

		if($nLenght < 4) {
			$nLenght = 4;
		}
		elseif($nLenght > 64) {
			$nLenght = 64;
		}
		if(!in_array($nLevel, array(1, 2, 3, 4))) {
			$nLevel = 4;
		}

		while (strlen($sReturn) < $nLenght) {

			while (TRUE) {
				$nLevelNow = rand(1, $nLevel);
				if(strlen($sReturn) < $nLevel) {
					if(!$aUsedLevels[$nLevelNow]) {
						break;
					}
				}
				else {
					break;
				}
			}

			$aUsedLevels[$nLevelNow] = TRUE;
			$sChars = self::$aChars[$nLevelNow];
			$nCharsLength = (strlen($sChars) - 1);
			$sChar = $sChars{rand(0, $nCharsLength)};

			if(0 == strlen($sReturn) or $sChar != $sReturn{strlen($sReturn) - 1}) {
				$sReturn .= $sChar;
			}
		}

		unset($nLenght, $nLevel, $aUsedLevels, $nLevelNow, $sChars, $nCharsLength, $sChar);
		return $sReturn;
	}

	/**
	 * Set Salt for SHA2
	 * 
	 * @param string $sSalt
	 */
	public static function setSalt($sSalt = '')
	{
		if(empty($sSalt)) {
			$sSalt = self::generatePassword(32);
		}

		self::$sSalt = (string) $sSalt;

		unset($sSalt);
	}

	/**
	 * Get Salt for SHA2
	 * @return type
	 */
	public static function getSalt()
	{
		return self::$sSalt;
	}

	/**
	 * Set hashing algorithm
	 * 
	 * @param string $sAlgo
	 */
	public static function setAlgorithm($sAlgorithm = 'sha256')
	{
		self::$sAlgorithm = (string) $sAlgorithm;

		unset($sAlgorithm);
	}

	/**
	 * Get used hashing algorithm
	 * @return string
	 */
	public static function getAlgorithm()
	{
		return self::$sAlgorithm;
	}

	/**
	 * SHA2 hash
	 * 
	 * @param string Any data
	 * @return string Hash
	 */
	static function SHA2($sString = '')
	{
		$sHashed = '';

		if(!empty($sString)) {
			if(function_exists('hash') and in_array(self::$sAlgorithm, hash_algos())) {
				$sHashed = hash_hmac(self::$sAlgorithm, $sString, self::$sSalt);
			}
			else {
				$sHashed = sha1(self::$sSalt . $sString);
			}
		}

		unset($sString);
		return $sHashed;
	}

}

# End of file