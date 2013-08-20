<?php

/**
 * LengthMax validator
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

namespace Luki\Validator;

use Luki\Validator\basicFactory;

/**
 * LengthMax validator
 * 
 * @package Luki
 */
class LengthMax extends basicFactory {

	public $sMessage = 'The length is greater then "%max%"!';
		
	public $max = 0;
	
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

		if($nLength <= $this->max) {
			$this->sError = '';
			$bReturn = TRUE;	
		}
		else {
			$this->sError = preg_replace('/%max%/', $this->max, $this->sMessage);
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