<?php

/**
 * Format template filter adapter
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
 * Format template filter
 * 
 * @package Luki
 */
class Luki_Template_Filter_Format {

	public function Get($sValue, $sPar1 = NULL, $sPar2 = NULL, $sPar3 = NULL, $sPar4 = NULL, $sPar5 = NULL, $sPar6 = NULL, $sPar7 = NULL, $sPar8 = NULL, $sPar9 = NULL, $sPar10 = NULL)
	{
		$sFnc = '$sReturn = sprintf($sValue';
		
		for($i=1; $i<11; $i++) {
			eval('$Par = is_null($sPar' . $i .');');

			if(!$Par) {
				$sFnc .= ', $sPar' . $i;
			}
		}
		
		$sFnc .= ');';
		
		eval($sFnc);
		
		unset($sValue, $sPar1, $sPar2, $sPar3, $sPar4, $sPar5, $sPar6, $sPar7, $sPar8, $sPar9, $sPar10);
		return $sReturn;
	}
}

# End of file