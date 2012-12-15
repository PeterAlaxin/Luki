<?php

/**
 * Between validator
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
 * Between validator
 * 
 * @package Luki
 */
class Luki_Validator_Between extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" is not between "%min%" and "%max%"!';
	
	public $min = 0;
	
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

		if($xValue > $this->min and $xValue < $this->max) {
			$this->sError = '';
			$bReturn = TRUE;
		}
		else {
			$this->sError = preg_replace('/%value%/', $xValue, $this->sMessage);
			$this->sError = preg_replace('/%min%/', $this->min, $this->sError);
			$this->sError = preg_replace('/%max%/', $this->max, $this->sError);
		}

		unset($xValue);
		return $bReturn;
	}


}

# End of file