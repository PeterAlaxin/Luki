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

namespace Luki;

use Luki\Cache\basicInterface;

/**
 * Cache class
 *
 * Caching data
 *
 * @package Luki
 */
class Cache {

	/**
	 * Chache adapter
	 * @var object 
	 * @access private
	 */
	private $oCacheAdapter = NULL;
	
	/**
	 * Default expiration
	 * @var int
	 */
	private $nExpiration = 0;

	public function __construct(basicInterface $oAdapter)
	{
		$this->oCacheAdapter = $oAdapter;

		unset($oAdapter);
	}

    public static function findAdapter($sType)
    {
        $sAdapter = __NAMESPACE__ . '\Cache\\' . $sType . 'Adapter';
        
        return $sAdapter;
    }

	public function setExpiration($nNewExpiration = 0)
	{
		$bReturn = FALSE;
		
		if(is_int($nNewExpiration)) {
			$this->nExpiration = (int)$nNewExpiration;
			$bReturn = TRUE;
		}

		unset($nNewExpiration);
		return $bReturn;
	}

	public function getExpiration()
	{
		return $this->nExpiration;
	}

	public function Set($sKey, $sValue = '', $nExpiration = NULL)
	{
        if(is_null($nExpiration)) {
            $nExpiration = $this->nExpiration;
        }
        
		if(is_array($sKey)) {
			$aKeyValues = $sKey;
			foreach ($aKeyValues as $sKey => $sValue) {
				$bReturn = $this->oCacheAdapter->Set($sKey, $sValue, $nExpiration);

				if(!$bReturn) {
					break;
				}
			}
		}
		else {
			$bReturn = $this->oCacheAdapter->Set($sKey, $sValue, $nExpiration);
		}

		unset($sKey, $sValue, $aKeyValues, $nExpiration);
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