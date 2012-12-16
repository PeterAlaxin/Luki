<?php

/**
 * IPv6 validator
 *
 * Luki framework
 * Date 16.12.2012
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
 * IPv6 validator
 * 
 * @package Luki
 */
class Luki_Validator_IPv6 extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" is not valid IPv6 address!';
	
	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;

		if(FALSE !== filter_var($xValue, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$this->sError = '';
			$bReturn = TRUE;
		}
		else {
			$this->sError = preg_replace('/%value%/', $xValue, $this->sMessage);
		}

		unset($xValue);
		return $bReturn;
	}

}

# End of file