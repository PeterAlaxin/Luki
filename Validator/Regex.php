<?php

/**
 * Regex validator
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
 * Regex validator
 * 
 * @package Luki
 */
class Luki_Validator_Regex extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" does not match regular expression "%regex%"!';

	public $regex = NULL;

	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;

		if(1 === preg_match($this->regex, $xValue)) {
			$this->sError = '';
			$bReturn = TRUE;
		}
		else {
			$this->sError = preg_replace('/%value%/', $xValue, $this->sMessage);
			$this->sError = preg_replace('/%regex%/', $this->regex, $this->sError);
		}

		unset($xValue);
		return $bReturn;
	}

}

# End of file