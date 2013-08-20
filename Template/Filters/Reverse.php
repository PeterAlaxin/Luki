<?php

/**
 * Reverse template filter adapter
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
 * Reverse template filter
 * 
 * @package Luki
 */
class Reverse {

	public function Get($sValue)
	{
		switch(gettype($sValue)) {
			case 'string':
				$aValue = preg_split("//u", $sValue, NULL, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
				$sReturn = implode('', array_reverse($aValue));
				break;
			case 'array':
				$sReturn = array_reverse($sValue);
				break;
			default:
				$sReturn = $sValue;
		}
		
		unset($sValue, $aValue);
		return $sReturn;
	}
}

# End of file