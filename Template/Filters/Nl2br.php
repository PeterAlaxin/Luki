<?php

/**
 * Nl2br template filter adapter
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
 * Nl2br template filter
 * 
 * @package Luki
 */
class Nl2br {

	public function Get($sValue)
	{
		$sReturn = nl2br($sValue, TRUE);
		
		unset($sValue);
		return $sReturn;
	}
}

# End of file