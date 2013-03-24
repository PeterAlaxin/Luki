<?php

/**
 * Capitalize template filter adapter
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
 * Capitalize template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Capitalize {

	public function Get($sValue)
	{
		$sValue = mb_convert_case($sValue, MB_CASE_UPPER, 'UTF-8');
		$sReturn = mb_substr($sValue, 0, 1, 'UTF-8') . 
				mb_convert_case(mb_substr($sValue, 1, mb_strlen($sValue, 'UTF-8') -1, 'UTF-8'), MB_CASE_LOWER, 'UTF-8');
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file