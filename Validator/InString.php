<?php

/**
 * InString validator
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
 * InString validator
 * 
 * @package Luki
 */
class Luki_Validator_InString extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" is not in the test string!';
		
	public $sString = array();
	
	public function __construct($aOptions)
	{
		$this->sString = $aOptions;
		
		unset($aOptions);
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

		if(1 == preg_match('/' . (string)$xValue . '/i', $this->sString)) {
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