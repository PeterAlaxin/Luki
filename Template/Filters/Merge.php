<?php

/**
 * Merge template filter adapter
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
 * Merge template filter
 * 
 * @package Luki
 */
class Merge {

	public function Get($sValue, $aMerge)
	{
		if(is_array($sValue) and is_array($aMerge)) {
			$aReturn = array_merge($sValue, $aMerge);
		}
		else {
			$aReturn = $sValue;
		}
		
		unset($sValue, $aMerge);
		return $aReturn;
	}
}

# End of file