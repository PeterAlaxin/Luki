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

namespace Luki\Template\Filters;

/**
 * Date template filter
 * 
 * @package Luki
 */
class Date {

	public function Get($dValue, $sFormat = '%d.%m.%Y', $sTimezone = '')
	{
        if(empty($sTimezone)) {
            $sTimezone = date_default_timezone_get();
        }
        
		$oTimezone = new \DateTimeZone($sTimezone);
		
		if(is_a($dValue, 'DateTime')) {
			$oDate = $dValue;
			$oDate->setTimezone($oTimezone);
		}
		else {
			$oDate = new \DateTime($dValue, $oTimezone);
		}
		
		$dReturn = strftime($sFormat, $oDate->getTimestamp());
        
		unset($dValue, $sFormat, $sTimezone, $oDate, $oTimezone);
		return $dReturn;
	}
}

# End of file