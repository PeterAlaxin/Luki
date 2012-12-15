<?php

/**
 * LengthMin validator
 *
 * Luki framework
 * Date 14.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Validator
 * @filesource
 */

/**
 * LengthMin validator
 * 
 * @package Luki
 */
class Luki_Validator_LengthMin extends Luki_Validator_Factory {

	public $sMessage = 'The length is less then "%min%"!';
		
	public $min = 0;
	
	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;
		$nLength = $this->_getLength($xValue);

		if($nLength >= $this->min) {
			$this->sError = '';
			$bReturn = TRUE;	
		}
		else {
			$this->sError = preg_replace('/%min%/', $this->min, $this->sMessage);
		}
				
		unset($xValue);
		return $bReturn;
	}

	private function _getLength($xValue)
	{
		$nLength = NULL;
		
		if(is_string($xValue)) {
			$nLength = strlen($xValue);
		}
		elseif(is_array($xValue)) {
			$nLength = count($xValue);
		}
		
		unset($xValue);
		return $nLength;
	}
	
}

# End of file