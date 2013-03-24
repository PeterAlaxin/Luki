<?php

/**
 * Trim template filter adapter
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
 * Trim template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Trim {

	public function Get($sValue, $sCharlist = '')
	{
		$sReturn = trim($sValue, ' \t\n\r\0\x0B' . $sCharlist);
		
		unset($sValue, $sCharlist);
		return $sReturn;
	}
}

# End of file