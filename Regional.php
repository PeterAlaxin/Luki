<?php

/**
 * Regional class
 *
 * Luki framework
 * Date 8.12.2012
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

/**
 * Regional class
 *
 * @package Luki
 */
class Luki_Regional {

	public static $sFormat = '%x';

	/**
	 * Set output date format
	 * 
	 * @param type $sFormat
	 */
	public static function setFormat($sFormat = '%x')
	{
		$bReturn = FALSE;
		$oDate = date_create('now');

		if(FALSE !== $oDate->format($sFormat)) {
			self::$sFormat = $sFormat;
			$bReturn = TRUE;
		}

		unset($sFormat);
		return $bReturn;
	}

	/**
	 * Get current format
	 */
	public static function getFormat()
	{
		return self::$sFormat;
	}

	/**
	 * Reset output date format to default
	 * 
	 * @param type $sFormat
	 */
	public static function resetFormat()
	{
		self::$sFormat = '%x';
	}

	/**
	 * Transform Date
	 *
	 * @param date $dValue Date
	 * @param string $sFormat User format
	 * @return date
	 */
	public static function Date($dValue = NULL, $sFormat = NULL)
	{
		if(empty($dValue) or
			'0000-00-00' == $dValue or
			($nMicroValue = strtotime($dValue)) === FALSE) {
			unset($dValue, $sFormat, $nMicroValue);
			return '';
		}

		$sOldLocale = NULL;

		switch ($sFormat) {
			case 'text':
				$sFormat = '%e. %B %Y, %A';
				break;
			case 'gmt':
				$sFormat = '%a, %d %b %Y %H:%M:%S GMT';
				$sOldLocale = setlocale(LC_TIME, 0);
				setlocale(LC_TIME, 'en_US.utf8');
				break;
			case NULL;
			default:
				$sFormat = self::$sFormat;
		}

		$dValue = strftime($sFormat, $nMicroValue);

		if(!is_null($sOldLocale)) {
			setlocale(LC_TIME, $sOldLocale);
		}

		unset($sFormat);
		return $dValue;
	}

	/**
	 * Transform Money
	 *
	 * @param float $nMoney Money
	 * @param string $sFormat User format
	 * @return string Formated money
	 */
	public static function Money($nMoney, $sFormat = NULL)
	{
		# Linux
		if(!empty($_SERVER["HTTP_USER_AGENT"]) and
			0 === preg_match('/windows/i', $_SERVER["HTTP_USER_AGENT"])) {

			switch ($sFormat) {
				case 'eur':
					$sFormat = '%!n&nbsp;€';
					break;
				default:
					$sFormat = '%!n';
			}

			$nMoney = money_format($sFormat, (float) $nMoney);
		}

		# Windows
		else {
			$nMoney = number_format((float) $nMoney, 2, ',', '.');

			if('eur' == $sFormat) {
				$nMoney = $nMoney . '&nbsp;€';
			}
		}

		unset($sFormat);
		return $nMoney;
	}

	/**
	 * Get days names
	 * 
	 * @param bool $bShort Short/Long names
	 * @return array
	 */
	public static function getDays($bShort = FALSE)
	{
		$aDays = array();

		$sFormat = '%A';
		if($bShort) {
			$sFormat = '%a';
		}

		for ($nDay = 1; $nDay < 8; $nDay++) {
			$nMicroValue = mktime(0, 0, 0, 1, $nDay, 2012);
			$aDays[] = strftime($sFormat, $nMicroValue);
		}

		unset($bShort, $nDay, $nMicroValue, $sFormat);
		return $aDays;
	}

	/**
	 * Get month names
	 * 
	 * @param bool $bShort Short/Long names
	 * @return array
	 */
	public static function getMonths($bShort = FALSE)
	{
		$aMonths = array();

		$sFormat = '%B';
		if($bShort) {
			$sFormat = '%b';
		}

		for ($nMonth = 1; $nMonth < 13; $nMonth++) {
			$nMicroValue = mktime(0, 0, 0, $nMonth, 1, 2012);
			$aMonths[] = strftime($sFormat, $nMicroValue);
		}

		unset($bShort, $nMonth, $nMicroValue, $sFormat);
		return $aMonths;
	}

	public static function setLocale($sLang)
	{
		setlocale(LC_ALL, $sLang);
		setlocale(LC_NUMERIC, 'C');

		unset($sLang);
	}

}

# End of file