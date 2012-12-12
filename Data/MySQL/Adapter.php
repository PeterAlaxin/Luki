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

	public $aLastID = array();
	
	public $nLastID = 0;

	public $aUpdated = array();
	
	public $nUpdated = 0;

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
			$this->saveLastID($sTable);
		}
		
		unset($sTable, $aValues, $sKey, $sValue, $sSQL, $bFirst, $oResultLast);
		return $oResult;
	}
	
	public function Update($sTable, $aValues, $aWhere)
	{
		$bResult = FALSE;
		
		if(!empty($sTable) and !empty($aValues)) {
					
			$bFirst = TRUE;
			$sSQL = 'UPDATE `' . $sTable . '` SET ';

			foreach($aValues as $sKey => $sValue) {
				if(!$bFirst) {
					$sSQL .= ', ';
				}

				$sSQL .= '`' . $sKey . '`="' . $this->escapeString($sValue) . '"';
			}
			
			if(!empty($aWhere)) {
				$bFirst = TRUE;
				$sSQL .= ' WHERE ';
				
				foreach($aWhere as $sKey => $sValue) {
					if(!$bFirst) {
						$sSQL .= ', ';
					}

					$sSQL .= '`' . $sKey . '`="' . $this->escapeString($sValue) . '"';
				}
			
			}
			
			$bResult = $this->Query($sSQL);
		}
		
		if(FALSE !== $bResult) {
			$this->saveUpdated($sTable);
		}

		unset($sTable, $aValues, $aWhere, $bFirst, $sSQL, $sKey, $sValue);
		return $bResult;
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
	
	public function getUpdated($sTable='')
	{
		$nUpdated = FALSE;
		
		if(empty($sTable)) {
			$nUpdated = $this->nUpdated;
		}
		elseif(isset($this->aUpdated[$sTable])) {
			$nUpdated = $this->aUpdated[$sTable];
		}
		
		unset($sTable);
		return $nUpdated;
	}
	
}

# End of file