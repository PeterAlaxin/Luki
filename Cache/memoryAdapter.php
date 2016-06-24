<?php

/**
 * Memory chache adapter
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

namespace Luki\Cache;

use Luki\Cache\basicInterface;
use Luki\Storage;

/**
 * Memory chache adapter
 * 
 * @package Luki
 */
class memoryAdapter implements basicInterface
{

    private $_memcache;

    public function __construct($options = array())
    {
        if ( empty($options) or ! is_array($options) ) {
            $options = array(
              'server' => 'localhost',
              'port' => '11211' );
        }
        $this->_memcache = new \Memcache;
        $this->_memcache->connect($options['server'], $options['port']);

        unset($options);
    }

    public function Set($key, $value = '', $expirationInSeconds = 0)
    {
        $isSet = $this->_memcache->set($key, serialize($value), MEMCACHE_COMPRESSED, $expirationInSeconds);

        if ( Storage::isProfiler() ) {
            Storage::Profiler()->Add('Cache', array('type' => 'write', 'key' => $key));
        }

        unset($key, $value, $expirationInSeconds);
        return $isSet;
    }

    public function Get($key)
    {
        $value = unserialize($this->_memcache->get($key, MEMCACHE_COMPRESSED));

        if ( Storage::isProfiler() ) {
            Storage::Profiler()->Add('Cache', array('type' => 'read', 'key' => $key));
        }

        unset($key);
        return $value;
    }

    public function Delete($key)
    {
        $isDeleted = $this->_memcache->delete($key);

        if ( Storage::isProfiler() ) {
            Storage::Profiler()->Add('Cache', array('type' => 'delete', 'key' => $key));
        }

        unset($key);
        return $isDeleted;
    }

}

# End of file