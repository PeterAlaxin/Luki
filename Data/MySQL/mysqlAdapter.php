<?php

/**
 * MySQL data adapter
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
 * MySQL data adapter
 * 
 * @package Luki
 */
class Luki_Data_MySQL_mysqlAdapter extends Luki_Data_MySQL_Adapter implements Luki_Data_Interface {

	public $rConnection = NULL;
	
	public $sSelectClass = 'Luki_Data_MySQL_Select';

	public $sResultClass = 'Luki_Data_MySQL_mysqlResult';
	
	public function __construct($aOptions)
	{
		$this->rConnection = mysql_connect(
			$aOptions['server'], 
			$aOptions['user'], 
			$aOptions['password']);
		
		if(!isset($this->rConnection) or FALSE === $this->rConnection) {
			echo 'Connection error';
			exit;
		}

	    if(!mysql_select_db($aOptions['database'], $this->rConnection)) {
			echo 'Connection error';
			exit;
	    }	
		
		$this->Query('SET CHARACTER_SET_CONNECTION=' . $aOptions['coding'] . ';');
		$this->Query('SET CHARACTER_SET_CLIENT=' . $aOptions['coding'] . ';');
		$this->Query('SET CHARACTER_SET_RESULTS=' . $aOptions['coding'] . ';');
		
		unset($aOptions);
	}
	
	public function Query($sSQL)
	{
		$oResult = mysql_query((string)$sSQL, $this->rConnection);
		
		if(is_resource($oResult)) {
			$oResult = new $this->sResultClass($oResult);
		}
		
		unset($sSQL);
		return $oResult;
	}
	
	public function escapeString($sString)
	{
		$sString = mysql_real_escape_string($sString, $this->rConnection);
		
		return $sString;
	}
	
	public function saveLastID($sTable)
	{
		$this->nLastID = mysql_insert_id($this->rConnection);
		$this->aLastID[$sTable] = $this->nLastID;
	}
	
	public function saveUpdated($sTable)
	{
		$this->nUpdated = mysql_affected_rows($this->rConnection);
		$this->aUpdated[$sTable] = $this->nUpdated;
	}
	
	public function saveDeleted($sTable)
	{
		$this->nDeleted = mysql_affected_rows($this->rConnection);
		$this->aDeleted[$sTable] = $this->nDeleted;
	}
}

# End of file