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
	
	public function addData($sName, $xData)
	{
		if(is_array($xData)) {
			$this->aData[$sName] = new Luki_Data($xData);
		}
		elseif(is_a($xData, 'Luki_Data')) {
			$this->aData[$sName] = $xData;
		}
		
		unset($sName, $xData);
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
	
}

# End of file