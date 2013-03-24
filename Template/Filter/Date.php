<?php

/**
 * Date template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
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
 * Date template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Date {

	public function Get($dValue, $sFormat = 'd.m.Y', $sTimezone = 'Europe/Berlin')
	{
		$oTimezone = new DateTimeZone($sTimezone);
		
		if(is_a($dValue, 'DateTime')) {
			$oDate = $dValue;
			$oDate->setTimezone($oTimezone);
		}
		else {
			$oDate = new DateTime($dValue, $oTimezone);
		}
		
		$dReturn = $oDate->format($sFormat);
	
		unset($dValue, $sFormat, $sTimezone, $oDate, $oTimezone);
		return $dReturn;
	}
}

# End of file