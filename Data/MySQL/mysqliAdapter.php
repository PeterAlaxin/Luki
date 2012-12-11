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

/**
 * MySQLi data adapter
 * 
 * @package Luki
 */
class Luki_Data_MySQL_mysqliAdapter extends Luki_Data_MySQL_Adapter implements Luki_Data_Interface {

	public $sSelectClass = 'Luki_Data_MySQL_Select';

	public $sResultClass = 'Luki_Data_MySQL_mysqliResult';
	
	public function __construct($aOptions)
	{
		$this->rConnection = mysqli_init();
		
		mysqli_options($this->rConnection, MYSQLI_OPT_LOCAL_INFILE, TRUE);
		
		mysqli_real_connect(
			$this->rConnection, 
			$aOptions['server'], 
			$aOptions['user'], 
			$aOptions['password'], 
			$aOptions['database']);

		if(mysqli_connect_errno() > 0) {
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
		$oResult = mysqli_query($this->rConnection, (string)$sSQL);

		if(is_a($oResult, 'mysqli_result')) {
			$oResult = new $this->sResultClass($oResult);
		}
		
		unset($sSQL);
		return $oResult;
	} 
	
	public function escapeString($sString)
	{
		$sString = mysqli_real_escape_string($this->rConnection, $sString);
		
		return $sString;
	}
}

# End of file