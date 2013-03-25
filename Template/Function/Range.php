<?php

/**
 * Range template function 
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
 * Range template function
 * 
 * @package Luki
 */
class Luki_Template_Function_Range {

	public function Get($nBegin, $nEnd, $nStep = 1)
	{
		$bConvert = FALSE;
		$bReverse = FALSE;
		$aRange = array();
		
		if(!is_numeric($nBegin) and !is_numeric($nEnd)) {
			$nBegin = ord($nBegin);
			$nEnd = ord($nEnd);
			$bConvert = TRUE;
		}

		$nStep = abs((int)$nStep);
		if(empty($nStep)) {
			$nStep = 1;
		}
				
		if($nBegin > $nEnd) {
			$nNewBegin = min($nBegin, $nEnd);
			$nNewEnd = max($nBegin, $nEnd);
			$bReverse = TRUE;
		}
		else {
			$nNewBegin = $nBegin;
			$nNewEnd = $nEnd;			
		}
		
		for($i = $nNewBegin; $i <= $nNewEnd; $i += $nStep) {
			if($bConvert) {
				$aRange[] = chr($i);
			}
			else {
				$aRange[] = $i;				
			}
		}
		
		if($bReverse) {
			$aRange = array_reverse($aRange);
		}
		
		$sReturn = "json_decode('" . json_encode($aRange) . "')";
		
		return $sReturn;
	}
}

# End of file