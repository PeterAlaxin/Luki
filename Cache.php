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

    const EXPIRE_IN_MINUTE = 60;
    const EXPIRE_IN_HALF_AN_HOUR = 1800;
    const EXPIRE_IN_HOUR = 3600;
    const EXPIRE_IN_DAY = 86400;
    
	/**
	 * Chache adapter
	 * @var object 
	 * @access private
	 */
	private $CacheAdapter = NULL;
	
	/**
	 * Default expiration
	 * @var int
	 */
	private $ExpirationInSeconds = 0;

	public function __construct(basicInterface $CacheAdapter)
	{
		$this->CacheAdapter = $CacheAdapter;

		unset($CacheAdapter);
	}

    public static function findAdapter($CacheType)
    {
        $CacheAdapter = __NAMESPACE__ . '\Cache\\' . $CacheType . 'Adapter';
        
        return $CacheAdapter;
    }

	public function setExpiration($nNewExpirationInSeconds = 0)
	{
		$isSet = FALSE;
		
		if(is_int($nNewExpirationInSeconds)) {
			$this->ExpirationInSeconds = $nNewExpirationInSeconds;
			$isSet = TRUE;
		}

		unset($nNewExpirationInSeconds);
		return $isSet;
	}

	public function getExpiration()
	{
		return $this->ExpirationInSeconds;
	}

	public function Set($Key, $Value = '', $ExpirationInSeconds = NULL)
	{
        if(is_null($ExpirationInSeconds)) {
            $ExpirationInSeconds = $this->ExpirationInSeconds;
        }
        
		if(is_array($Key)) {
			foreach ($Key as  $SubKey => $SubValue) {
				$isSet = $this->CacheAdapter->Set($SubKey, $SubValue, $ExpirationInSeconds);

				if(!$isSet) {
					break;
				}
			}
		}
		else {
			$isSet = $this->CacheAdapter->Set($Key, $Value, $ExpirationInSeconds);
		}

		unset($Key, $Value, $SubKey, $SubValue, $ExpirationInSeconds);
		return $isSet;
	}

	public function Get($Key)
	{
		$Value = $this->CacheAdapter->Get($Key);

		unset($Key);
		return $Value;
	}

	public function Delete($Key)
	{
		$isDeleted = $this->CacheAdapter->Delete($Key);

		unset($Key);
		return $isDeleted;
	}

}

# End of file