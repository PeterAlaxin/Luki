<?php

/**
 * MySQLi data adapter
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

namespace Luki\Data\MySQL;

use Luki\Data\MySQL\basicAdapter;
use Luki\Data\MySQL\Select as Select;
use Luki\Data\MySQL\mysqliResult as Result;

/**
 * MySQLi data adapter
 * 
 * @package Luki
 */
class mysqliAdapter extends basicAdapter {

	public $oMySQL = NULL;
	
	public function __construct($aOptions)
	{
		$this->oMySQL = new \mysqli(
			$aOptions['server'], 
			$aOptions['user'], 
			$aOptions['password'], 
			$aOptions['database']);
		
		if(!empty($this->oMySQL->connect_error)) {
			echo 'Connection error: ' . $this->oMySQL->connect_error;
			exit;
		}
		
		$this->Query('SET CHARACTER_SET_CONNECTION=' . $aOptions['coding'] . ';');
		$this->Query('SET CHARACTER_SET_CLIENT=' . $aOptions['coding'] . ';');
		$this->Query('SET CHARACTER_SET_RESULTS=' . $aOptions['coding'] . ';');
		
		unset($aOptions);
	}
	
    public function __destruct()
    {
        $this->oMySQL->close();
    }
    
	public function Select()
	{
		$oSelect = new Select($this);
		
		return $oSelect;
	}
	
	public function Query($sSQL)
	{
		$oResult = $this->oMySQL->query((string)$sSQL);

		if(is_a($oResult, 'mysqli_result')) {
			$oResult = new Result($oResult);
		}
		
		unset($sSQL);
		return $oResult;
	} 
	
	public function escapeString($sString)
	{
		$sString = $this->oMySQL->real_escape_string($sString);
		
		return $sString;
	}
	
	public function saveLastID($sTable)
	{
		$this->nLastID = $this->oMySQL->insert_id;
		$this->aLastID[$sTable] = $this->nLastID;
	}
	
	public function saveUpdated($sTable)
	{
		$this->nUpdated = $this->oMySQL->affected_rows;
		$this->aUpdated[$sTable] = $this->nUpdated;
	}
	
	public function saveDeleted($sTable)
	{
		$this->nDeleted = $this->oMySQL->affected_rows;
		$this->aDeleted[$sTable] = $this->nDeleted;
	}
}

# End of file