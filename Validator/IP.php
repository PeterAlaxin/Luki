<?php

/**
 * IP validator
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
 * IP validator
 * 
 * @package Luki
 */
class Luki_Validator_IP extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" is not valid IPv4 or IPv6 address!';
	
	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;

		if(FALSE !== filter_var($xValue, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) or 
		   FALSE !== filter_var($xValue, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
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