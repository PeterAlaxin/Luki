<?php

/**
 * Model class
 *
 * Luki framework
 * Date 6.1.2013
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
 * Model class
 *
 * @package Luki
 */
abstract class Luki_Model {
	
	public $aData = array();
	
	public function addData($sName, Luki_Data_Interface $oDataAdapter)
	{
		$this->aData[$sName] = new Luki_Data($oDataAdapter);
		
		unset($sName, $oDataAdapter);
		return $this;
	}
	
	public function getData($sName)
	{
		$oReturn = NULL;
		
		if(isset($this->aData[$sName])) {
			$oReturn = $this->aData[$sName];
		}
		
		unset($sName);
		return $oReturn;
	}
	
	public function getAdapter($aOptions) 
	{
		$oAdapter = FALSE;

		if(!empty($aOptions['adapter'])) {
			$sAdapterName = 'Luki_Data_' . $aOptions['adapter'] . 'Adapter';
		
			if(Luki_Loader::isClass($sAdapterName)) {
				$oAdapter = new $sAdapterName($aOptions);
			}
		}
		
		return $oAdapter;
	}
}

# End of file