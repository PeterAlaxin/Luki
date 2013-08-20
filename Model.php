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

namespace Luki;

use Luki\Data;
use Luki\Data\basicInterface;

/**
 * Model class
 *
 * @package Luki
 */
abstract class Model {
	
	public $aData = array();
	
	public function addData($sName, basicInterface $oDataAdapter)
	{
		$this->aData[$sName] = new Data($oDataAdapter);
		
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
			$sAdapterName = $aOptions['adapter'] . 'Adapter';		
			$oAdapter = new $sAdapterName($aOptions);
		}
		
		return $oAdapter;
	}
}

# End of file