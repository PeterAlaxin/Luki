<?php

/**
 * Bites template filter adapter
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
 * Bites template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Bites {

	public function Get($nValue)
	{
		if($nValue < 1024) {
			$nReturn = number_format($nValue, 0) . '&nbsp;B';
		}
		elseif($nValue < 1048576) {
			$nReturn = number_format($nValue/1024, 2, ',', '.') . '&nbsp;kB';
		}
		elseif($nValue < 1073741824) {
			$nReturn = number_format($nValue/1048576, 2, ',', '.') . '&nbsp;MB';
		}
		else {
			$nReturn = number_format($nValue/1073741824, 2, ',', '.') . '&nbsp;GB';
		}

		unset($nValue);
		return $nReturn;
	}
}

# End of file