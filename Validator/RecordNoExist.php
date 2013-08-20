<?php

/**
 * RecordNoExist validator
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
 * RecordNoExist validator
 * 
 * @package Luki
 */
class RecordNoExist extends basicFactory {

	public $sMessage = 'The record with ID="%value%" exists!';

	public $data = NULL;
	
	public $table = NULL;
	
	public $key = NULL;

	/**
	 * Validation
	 * 
	 * @param mixed $xValue 
	 * @return bool
	 */
	public function isValid($xValue)
	{
		$bReturn = FALSE;
		
		$oSelect = $this->data->Select();
		$oSelect->from($this->table, array($this->key))
				->where($this->key . '=?', $xValue)
				->limit(1);
		
		$oResult = $this->data->Query($oSelect);

		if(0 == $oResult->getRecordsCount()) {
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