<?php

/**
 * Join template filter adapter
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
 * Join template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Join {

	public function Get($sValue, $sSeparator = '')
	{
		if(is_array($sValue)) {
			$sReturn = implode($sSeparator, $sValue);
		}
		else {
			$sReturn = $sValue;
		}
		
		unset($sValue, $sSeparator);
		return $sReturn;
	}
}

# End of file