<?php

/**
 * Data Adapter interface
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
 * Data Adapter interface
 * 
 * @package Luki
 */
interface Luki_Data_Interface {

	public function __construct($aOptions);

	public function Query($sSQL);

	public function Select();

	public function Insert($sTable, $aValues);

	public function Update($sTable, $aValues, $sWhere);
	
	public function Delete($sTable, $sWhere);

	public function getLastID($sTable);

	public function getUpdated($sTable);
	
	public function getDeleted($sTable);

	public function escapeString($sString);
	
	public function saveLastID($sTable);
	
	public function saveUpdated($sTable);

	public function saveDeleted($sTable);
}

# End of file