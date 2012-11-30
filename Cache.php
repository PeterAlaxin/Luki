<?php
/**
 * Cache class
 *
 * Luki framework
 * Date 24.9.2012
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
 * Cache class
 *
 * Caching data
 *
 * @package Luki
 */
class Luki_Cache {

	/**
	 * Chache adapter
	 * @var object 
	 * @access private
	 */
	private $oCacheAdapter = NULL;

	private $nExpiration = 0;

	public function __construct($sAdapter='memory', $aOptions=array())
	{	
		$sAdapterClass = 'Luki_Cache_' . $sAdapter . 'Adapter';
		
		$this->oCacheAdapter = new $sAdapterClass($aOptions);

		unset($sAdapter, $sAdapterClass);
	}
	
	public function setExpiration($nNewExpiration=0)
	{
		$this->nExpiration = (int)$nNewExpiration;
		
		unset($nNewExpiration);
	}
	
	public function getExpiration()
	{
		return $this->nExpiration;
	}
	
	public function Set($sKey='', $sValue='')
	{
		if(is_array($sKey)) {
			$aKeyValues = $sKey;
			foreach($aKeyValues as $sKey => $sValue) {
				$bReturn = $this->oCacheAdapter->Set($sKey, $sValue, $this->nExpiration);
				
				if(!$bReturn) {
					break;
				}
			}
		}
		else {
			$bReturn = $this->oCacheAdapter->Set($sKey, $sValue, $this->nExpiration);
		}
		
		unset($sKey, $sValue, $aKeyValues);
		return $bReturn;
	}
	
	public function Get($sKey)
	{
		$sReturn = $this->oCacheAdapter->Get($sKey);
		
		unset($sKey);
		return $sReturn;
	}
	
	public function Delete($sKey)
	{
		$sReturn = $this->oCacheAdapter->Delete($sKey);
		
		unset($sKey);
		return $sReturn;
	}
	
}

# End of file