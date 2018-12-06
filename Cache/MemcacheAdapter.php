<?php
/**
 * Memcache chache adapter
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

namespace Luki\Cache;

use Luki\Cache\BasicAdapter;
use Luki\Cache\BasicInterface;
use Luki\Storage;

class MemcacheAdapter extends BasicAdapter implements BasicInterface
{
    public $messagge = 'Memcache is not supported. Install it or use another cache adapter.';
    private $cache;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->cache = new \Memcache;
        $this->cache->connect($this->server, $this->port);

        unset($options);
    }

    public function isSupported()
    {
        $isSupported = extension_loaded('Memcache');

        return $isSupported;
    }

    public function Set($key, $value, $expiration)
    {
        $isSet = $this->cache->set($key, serialize($value), MEMCACHE_COMPRESSED, $expiration);
        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Cache', array('type' => 'write', 'key' => $key));
        }

        return $isSet;
    }

    public function Get($key)
    {
        $value = $this->cache->get($key, MEMCACHE_COMPRESSED);

        if ((bool) $value) {
            $value = unserialize($value);
            if (Storage::isProfiler()) {
                Storage::Profiler()->Add('Cache', array('type' => 'read', 'key' => $key));
            }
        } else {
            $value = null;
        }

        return $value;
    }

    public function Delete($key)
    {
        $value = $this->cache->get($key, MEMCACHE_COMPRESSED);

        if ((bool) $value) {
            $isDeleted = $this->cache->delete($key);
            if (Storage::isProfiler()) {
                Storage::Profiler()->Add('Cache', array('type' => 'delete', 'key' => $key));
            }
        } else {
            $isDeleted = false;
        }

        return $isDeleted;
    }

    public function Has($key)
    {
        $has = (bool) $this->cache->get($key, MEMCACHE_COMPRESSED);

        return $has;
    }

    public function Clear()
    {
        $this->cache->flush();
    }
}