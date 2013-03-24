<?php

/**
 * Numberformat template filter adapter
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
 * Numberformat template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Numberformat {

	public function Get($nValue, $nDecimal = 2, $sDecimals = ',', $sThousands = '.')
	{
		$nReturn = number_format($nValue, $nDecimal, $sDecimals, $sThousands);

		unset($nValue);
		return $nReturn;
	}
}

# End of file