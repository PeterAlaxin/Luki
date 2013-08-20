<?php

/**
 * Money template filter adapter
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
 * Money template filter
 * 
 * @package Luki
 */
class Money {

	public function Get($nValue, $sFormat = NULL)
	{
		if(!empty($_SERVER["HTTP_USER_AGENT"]) and 0 === preg_match('/windows/i', $_SERVER["HTTP_USER_AGENT"])) {
			if(empty($sFormat)) {
				$sReturn = money_format('%!n', (float)$nValue);
			}
			elseif('eur' == $sFormat) {
				$sReturn = money_format('%!n&nbsp;€', (float)$nValue);
			}
			else {
				$sReturn = money_format($sFormat, (float)$nValue);
			}
		}
		else {
			$sReturn = number_format((float)$nValue, 2, ',', '.');
			
			if('eur' == $sFormat) {
				$sReturn .= '&nbsp;€';
			}
		}
		
		unset($nValue, $sFormat);
		return $sReturn;
	}	

}

# End of file