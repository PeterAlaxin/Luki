<?php

/**
 * Data class
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
 * Data class
 *
 * Data access
 *
 * @package Luki
 */
class Luki_Data {

	private $oDataAdapter = NULL;

	/**
	 * Data constructor
	 */
	public function __construct($aOptions)
	{
		$sAdapterClass = 'Luki_Data_' . $aOptions['adapter'] . 'Adapter';
		$this->oDataAdapter = new $sAdapterClass($aOptions);

		unset($aOptions);
	}

	public function Select()
	{
		$oSelect = $this->oDataAdapter->Select();

		return $oSelect;
	}

	public function Insert($sTable, $aValues)
	{
		$nLastID = $this->oDataAdapter->Insert($sTable, $aValues);
		
		unset($sTable, $aValues);
		return $nLastID;
	}
	
	public function Update($sTable, $aValues, $aWhere=NULL)
	{
		$bResult = $this->oDataAdapter->Update($sTable, $aValues, $aWhere);
		
		unset($sTable, $aValues, $aWhere);
		return $bResult;
	}
	
	public function Delete($sTable, $aWhere=NULL)
	{
		$bResult = $this->oDataAdapter->Delete($sTable, $aWhere);
		
		unset($sTable, $aWhere);
		return $bResult;
	}
	
	public function Query($sSelect)
	{
		$oResult = $this->oDataAdapter->Query($sSelect);

		unset($sSelect);
		return $oResult;
	}

	public function getLastID($sTable='')
	{
		$nLastID = $this->oDataAdapter->getLastID($sTable);
		
		unset($sTable);
		return $nLastID;
	}

	public function getUpdated($sTable='')
	{
		$nUpdated = $this->oDataAdapter->getUpdated($sTable);
		
		unset($sTable);
		return $nUpdated;
	}

	public function getDeleted($sTable='')
	{
		$nDeleted = $this->oDataAdapter->getDeleted($sTable);
		
		unset($sTable);
		return $nDeleted;
	}
}

# End of file