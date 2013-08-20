<?php

/**
 * LessThan validator
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
 * LessThan validator
 * 
 * @package Luki
 */
class LessThan extends basicFactory {

	public $sMessage = 'The value "%value%" not less then "%max%"!';
	
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

		if($xValue < $this->max) {
			$this->sError = '';
			$bReturn = TRUE;
		}
		else {
			$this->sError = preg_replace('/%value%/', $xValue, $this->sMessage);
			$this->sError = preg_replace('/%max%/', $this->max, $this->sError);
		}

		unset($xValue);
		return $bReturn;
	}


}

# End of file