<?php

/**
 * InArray validator
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
 * InArray validator
 * 
 * @package Luki
 */
class Luki_Validator_InArray extends Luki_Validator_Factory {

	public $sMessage = 'The value "%value%" is not in the test array!';
		
	public $aValues = array();
	
	public function __construct($aOptions)
	{
		$this->aValues = $aOptions;
		
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

		if(in_array($xValue, $this->aValues)) {
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