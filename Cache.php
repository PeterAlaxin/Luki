<?php

/**
 * Cache class
 *
 * Luki framework
 * Date 24.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
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
class Cache
{

    const EXPIRE_IN_MINUTE = 60;
    const EXPIRE_IN_HALF_AN_HOUR = 1800;
    const EXPIRE_IN_HOUR = 3600;
    const EXPIRE_IN_DAY = 86400;

    private $_cacheAdapter = NULL;
    private $_expirationInSeconds = 0;
    private $_useCache = TRUE;

    public function __construct(basicInterface $cacheAdapter)
    {
        $this->_cacheAdapter = $cacheAdapter;

        unset($cacheAdapter);
    }

    public static function findAdapter($cacheType)
    {
        $cacheAdapter = __NAMESPACE__ . '\Cache\\' . $cacheType . 'Adapter';

        return $cacheAdapter;
    }

    public function setExpiration($newExpirationInSeconds = 0)
    {
        $isSet = FALSE;

        if ( is_int($newExpirationInSeconds) ) {
            $this->_expirationInSeconds = $newExpirationInSeconds;
            $isSet = TRUE;
        }

        unset($newExpirationInSeconds);
        return $isSet;
    }

    public function getExpiration()
    {
        return $this->_expirationInSeconds;
    }

    public function Set($key, $value = '', $expirationInSeconds = NULL)
    {
        if ( !$this->_useCache ) {
            return NULL;
        }

        if ( is_null($expirationInSeconds) ) {
            $expirationInSeconds = $this->_expirationInSeconds;
        }

        if ( is_array($key) ) {
            foreach ( $key as $subKey => $subValue ) {
                $isSet = $this->_cacheAdapter->Set($subKey, $subValue, $expirationInSeconds);

                if ( !$isSet ) {
                    break;
                }
            }
        } else {
            $isSet = $this->_cacheAdapter->Set($key, $value, $expirationInSeconds);
        }

        unset($key, $value, $subKey, $subValue, $expirationInSeconds);
        return $isSet;
    }

    public function Get($key)
    {
        if ( !$this->_useCache ) {
            $value = NULL;
        } else {
            $value = $this->_cacheAdapter->Get($key);
        }

        unset($key);
        return $value;
    }

    public function Delete($key)
    {
        $isDeleted = $this->_cacheAdapter->Delete($key);

        unset($key);
        return $isDeleted;
    }

    public function useCache($useCache = TRUE)
    {
        $this->_useCache = (bool) $useCache;
    }

    public function isUsedCache()
    {
        return $this->_useCache;
    }

}

# End of file