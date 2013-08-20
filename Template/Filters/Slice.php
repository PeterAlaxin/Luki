<?php

/**
 * Slice template filter adapter
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
 * Slice template filter
 * 
 * @package Luki
 */
class Slice {

	public function Get($sValue, $nStart = 0, $nLength = 1)
	{
		switch(gettype($sValue)) {
			case 'string':
				$sReturn = mb_substr($sValue, $nStart, $nLength, 'UTF-8');
				break;
			case 'array':
				$sReturn = array_slice($sValue, $nStart, $nLength);
				break;
			default:
				$sReturn = $sValue;
		}
		
		unset($sValue, $nStart, $nLength);
		return $sReturn;
	}
}

# End of file