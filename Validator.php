<?php

/**
 * Validator class
 *
 * Luki framework
 * Date 16.12.2012
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
 * Validation aggregation class
 *
 * @package Luki
 */
class Validator {

	private static $sError = '';

	public static function isValid($xValue, $sValidator, $aOptions = array(), $sMessage = '')
	{
		$oValidator = new $sValidator($aOptions);
		self::$sError = '';

		if(!empty($sMessage)) {
			$oValidator->setMessage($sMessage);
		}

		$bReturn = $oValidator->isValid($xValue);

		if(!$bReturn) {
			self::$sError = $oValidator->getError();
		}

		unset($xValue, $sValidator, $aOptions, $oValidator, $sMessage);
		return $bReturn;
	}

	public static function getError()
	{
		return self::$sError;
	}

}

# End of file