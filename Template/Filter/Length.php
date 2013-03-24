<?php

/**
 * Length template filter adapter
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
 * Length template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Length {

	public function Get($sValue)
	{
		switch(gettype($sValue)) {
			case 'string':
				$nReturn = strlen($sValue);
				break;
			case 'array':
				$nReturn = count($sValue);
				break;
			default:
				$nReturn = $sValue;
		}
		
		unset($sValue);
		return $nReturn;
	}
}

# End of file