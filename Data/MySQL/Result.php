<?php

/**
 * MySQL Result Iterator class
 *
 * Luki framework
 * Date 9.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Data\MySQL;

/**
 * MySQL Result Iterator class
 *
 * @package Luki
 */
class Result implements \Iterator {

	/**
	 * Position
	 * @var integer
	 */
	public $nPosition = 0;

	/**
	 * Result
	 * @var object
	 */
	public $oResult = NULL;

	/**
	 * Row data
	 * @var array
	 */
	public $aRow = NULL;

	/**
	 * Records count
	 * @var integer
	 */
	public $nRecords = 0;

	public function rewind()
	{
		$this->nPosition = 0;
		$this->_setRecord();
		
		return $this;
	}

	public function current()
	{
		return $this->aRow;
	}

	public function key()
	{
		return $this->nPosition;
	}

	public function next()
	{
		$this->nPosition++;
		$this->_setRecord();
		
		return $this;
	}

	public function valid()
	{
		return (boolean)$this->aRow;
	}

	public function first()
	{
		$this->nPosition = 0;
		$this->_setRecord();
		
		return $this;
	}

	public function last()
	{
		$this->nPosition = $this->nRecords - 1;
		$this->_setRecord();
		
		return $this;
	}

	public function getRecordsCount()
	{
		return $this->nRecords;
	}

	public function Get($sKey)
	{
		$xReturn = NULL;
		if(isset($this->aRow[$sKey])) {
			$xReturn = $this->aRow[$sKey];
		}

		unset($sKey);
		return $xReturn;
	}
	
	/**
	 * Get all rows
	 */
	public function getAllRows()
	{
		$aAllRows = array();

		foreach($this as $aRow) {
			$aAllRows[] = $aRow;
		}

		unset($aRow);
		return $aAllRows;
	}
	
	/**
	 * Get one row
	 *
	 * @return array
	 */
	public function getRow()
	{
		return $this->aRow;
	}
	
}

# End of file