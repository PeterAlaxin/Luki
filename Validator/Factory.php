<?php

/**
 * Validator factory
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
 * Validator factory
 * 
 * @abstract
 * @package Luki
 */
abstract class Luki_Validator_Factory implements Luki_Validator_Interface {

	public $sError = '';

	public function __construct($aOptions=array())
	{
		foreach($aOptions as $sKey => $xValue) {
			$this->$sKey = $xValue;
		}
		
		unset($aOptions, $sKey, $xValue);
	}
	
	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;

		if(1 === preg_match($this->sValidator, $xValue)) {
			$this->sError = '';
			$bReturn = TRUE;
		}
		else {
			$this->sError = preg_replace('/%value%/', $xValue, $this->sMessage);
		}

		unset($xValue);
		return $bReturn;
	}

	public function setMessage($sMessage)
	{
		$this->sMessage = $sMessage;
	}

	public function getError()
	{
		return $this->sError;
	}

}

# End of file