<?php

/**
 * Lower template filter adapter
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
 * Lower template filter
 * 
 * @package Luki
 */
class Lower {

	public function Get($sValue)
	{
		$sReturn = mb_convert_case($sValue, MB_CASE_LOWER, 'UTF-8');
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file