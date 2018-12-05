<?php
/**
 * Cache class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Cache
 * @filesource
 */

namespace Luki;

use Luki\Cache\BasicInterface;

class Cache
{
    const EXPIRE_IN_MINUTE       = 60;
    const EXPIRE_IN_HALF_AN_HOUR = 1800;
    const EXPIRE_IN_HOUR         = 3600;
    const EXPIRE_IN_DAY          = 86400;

    private $cacheAdapter = null;
    private $expiration   = 0;
    private $useCache     = true;
    private $isPrivate    = false;
    private $privateKey   = '';

    public function __construct(BasicInterface $adapter)
    {
        $this->cacheAdapter = $adapter;
        $this->expiration   = $this->cacheAdapter->getExpiration();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function findAdapter($cacheType)
    {
        $cacheAdapter = __NAMESPACE__.'\Cache\\'.$cacheType.'Adapter';

        return $cacheAdapter;
    }

    public function setExpiration($expiration = 0)
    {
        if (is_int($expiration)) {
            $this->expiration = $expiration;
            $isSet            = true;
        } else {
            $isSet = false;
        }

        return $isSet;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function Set($key, $value = '', $expiration = null)
    {
        if (!$this->useCache) {
            return null;
        }

        if (!is_int($expiration)) {
            $expiration = $this->expiration;
        }

        if (is_array($key)) {
            foreach ($key as $subKey => $subValue) {
                $isSet = $this->cacheAdapter->Set($subKey, $subValue, $expiration);
                if (!$isSet) {
                    break;
                }
            }
        } else {
            $isSet = $this->cacheAdapter->Set($key, $value, $expiration);
        }

        return $isSet;
    }

    public function Get($key)
    {
        if (!$this->useCache) {
            $value = null;
        } else {
            $value = $this->cacheAdapter->Get($key);
        }

        return $value;
    }

    public function Delete($key)
    {
        $isDeleted = $this->cacheAdapter->Delete($key);

        return $isDeleted;
    }

    public function Clear()
    {
        $this->cacheAdapter->Clear();
    }

    public function Has($key)
    {
        $has = $this->cacheAdapter->Has($key);

        return $has;
    }

    public function useCache($useCache = true)
    {
        $this->useCache = (bool) $useCache;
    }

    public function isUsedCache()
    {
        return $this->useCache;
    }

    public function setPrivate($isPrivate = true)
    {
        $this->isPrivate  = (bool) $isPrivate;
        $this->privateKey = session_id();

        return $this;
    }

    public function isPrivate()
    {
        return $this->isPrivate;
    }
}