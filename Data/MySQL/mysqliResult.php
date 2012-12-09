<?php

/**
 * MySQLi Result Iterator class
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

/**
 * MySQLi Result Iterator class
 *
 * @package Luki
 */
class Luki_Data_MySQL_mysqliResult extends Luki_Data_MySQL_Result implements Iterator {

	/**
	 * Constructor
	 *
	 * @param object|resource SQL result
	 * @uses Result::fetchRow() Fetch one row from result
	 */
	function __construct($oResult)
	{
		$this->oResult = $oResult;

		if(is_object($this->oResult)) {
			$this->nRecords = mysqli_num_rows($this->oResult);
			
			if(!empty($this->nRecords)) {
				$this->rewind();
			}
		}
		
		unset($oResult);
	}
	
	public function _setRecord()
	{
		$this->aRow = FALSE;
		
		if( $this->nPosition >= 0 and 
			$this->nPosition < $this->nRecords and 
			mysqli_data_seek($this->oResult, $this->nPosition)) {
			$this->aRow = mysqli_fetch_array($this->oResult, MYSQL_ASSOC);
		}
	}

}

# End of file