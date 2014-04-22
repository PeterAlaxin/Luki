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
use Luki\Storage;
use Luki\Url;

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

    public function getFromCache($sName='') 
    {
        $xCache = FALSE;
        
        if(Storage::isCache() and Storage::Cache()->isUsedCache()) {            
            $sName = $this->_getCacheName($sName);        
            $xCache = Storage::Cache()->Get($sName);
        }
        
        unset($sName);
        return $xCache;
    }
    
    public function setToCache($sContent, $sName='', $nExpiration=3600)
    {
        if(Storage::isCache()) {
            $sName = $this->_getCacheName($sName);
            Storage::Cache()->Set($sName, $sContent, $nExpiration);
        }
        
        unset($sContent, $sName, $nExpiration);
    }
    
    private function _getCacheName($sName)
    {
        $aCallers = debug_backtrace();
        $sNewName = $aCallers[2]['class'] . '_' . $aCallers[2]['function'];

        if(!empty($sName)) {
            $sNewName .= '_' . $sName;
        }
        elseif(!empty($aCallers[2]['args'])) {
            $sNewName .= '_' . Url::makeLink(implode('_', $aCallers[2]['args']), FALSE);
        }
        
        unset($aCallers, $sName);
        return $sNewName;
    }
}

# End of file