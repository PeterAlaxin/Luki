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
class Luki_Data_MySQL_mysqlAdapter implements Luki_Data_Interface {

	private $rConnection = NULL;
	
	private $sSelectClass = 'Luki_Data_MySQL_Select';

	private $sResultClass = 'Luki_Data_MySQL_mysqlResult';
	
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
	
	public function Select()
	{
		$oSelect = new $this->sSelectClass($this);
		
		return $oSelect;
	}
	
	public function Query($sSQL)
	{
		$oResult = mysql_query((string)$sSQL, $this->rConnection);
		$oResult = new $this->sResultClass($oResult);
		
		unset($sSQL);
		return $oResult;
	}
	
	public function escapeString($sString)
	{
		$sString = mysql_real_escape_string($sString, $this->rConnection);
		
		return $sString;
	}
}

# End of file