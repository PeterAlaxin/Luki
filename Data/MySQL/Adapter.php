<?php

/**
 * Data adapter
 *
 * Luki framework
 * Date 9.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

/**
 * Data adapter
 * 
 * @package Luki
 */
class Luki_Data_MySQL_Adapter {

	public $rConnection = NULL;
	
	public $sSelectClass = '';

	public $sResultClass = '';
	
	public $aLastID = array();
	
	public $nLastID = 0;

	public function Select()
	{
		$oSelect = new $this->sSelectClass($this);
		
		return $oSelect;
	}
	
	public function Insert($sTable, $aValues)
	{
		$bFirst = TRUE;
		$sSQL = 'INSERT INTO `' . $sTable . '` SET ';
		
		foreach($aValues as $sKey => $sValue) {
			if(!$bFirst) {
				$sSQL .= ', ';
			}
			
			$sSQL .= '`' . $sKey . '`="' . $this->escapeString($sValue) . '"';
		}
		
		$oResult = $this->Query($sSQL);

		if(FALSE !== $oResult) {
			$sSQL = 'SELECT LAST_INSERT_ID() AS lastID;';
			$oResultLast = $this->Query($sSQL);
			$this->nLastID = $oResultLast->Get('lastID');
			$this->aLastID[$sTable] = $this->nLastID;
		}
		
		unset($sTable, $aValues, $sKey, $sValue, $sSQL, $bFirst, $oResultLast);
		return $oResult;
	}
	
	public function getLastID($sTable='')
	{
		$nLastID = FALSE;
		
		if(empty($sTable)) {
			$nLastID = $this->nLastID;
		}
		elseif(isset($this->aLastID[$sTable])) {
			$nLastID = $this->aLastID[$sTable];
		}
		
		unset($sTable);
		return $nLastID;
	}
	
}

# End of file