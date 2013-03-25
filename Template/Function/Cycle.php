<?php

/**
 * Cycle template function 
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
 * Cycle template function
 * 
 * @package Luki
 */
class Luki_Template_Function_Cycle {

	public function Get($aSource, $nValue)
	{
		if(count($aSource) <= $nValue) {
			$nRatio = ceil($nValue/count($aSource));
			$aFinal = $aSource; 

			for($i=1; $i<=$nRatio; $i++) {
				$aSource = array_merge($aSource, $aFinal);
			}
		}
			
		$sReturn = $aSource[$nValue];
		
		unset($aSource, $nValue, $aFinal, $i, $nRatio);
		return $sReturn;
	}
}

# End of file