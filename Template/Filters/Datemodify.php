<?php

/**
 * Datemodify template filter adapter
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
 * Datemodify template filter
 * 
 * @package Luki
 */
class Datemodify {

	public function Get($dValue, $sModifier)
	{
		$oDate = new \DateTime($dValue);
		
		$dReturn = $oDate->modify($sModifier);

		unset($dValue, $oDate, $sModifier);
		return $dReturn;
	}
}

# End of file