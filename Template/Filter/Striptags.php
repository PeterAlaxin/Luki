<?php

/**
 * Striptags template filter adapter
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
 * Striptags template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Striptags {

	public function Get($sValue)
	{
		$sReturn = strip_tags($sValue);
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file