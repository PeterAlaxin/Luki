<?php

namespace Luki\Cache;

use Luki\Cache\BasicAdapter;
use Luki\Cache\BasicInterface;

/**
 * Yac chache adapter
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
final class YacAdapter extends BasicAdapter implements BasicInterface
{
    public $messagge = 'Yac is not supported. Install it or use another cache adapter.';
    private $cache;

    function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->cache = new \Yac();

        unset($options);
    }

    public function isSupported()
    {
        $isSupported = extension_loaded('Yac') and ini_get('yac.enabled');

        return $isSupported;
    }

    public function Set($key, $value, $expiration)
    {
        $isSet = $this->cache->set($key, $value, $expiration);

        return $isSet;
    }

    public function Get($key)
    {
        $value = $this->cache->get($key);

        if ((bool) $value) {
        } else {
            $value = NULL;
        }

        return $value;
    }

    public function Delete($key)
    {
        $value = $this->cache->get($key);

        if ((bool) $value) {
            $isDeleted = $this->cache->delete($key);
        } else {
            $isDeleted = FALSE;
        }

        return $isDeleted;
    }

    public function Has($key)
    {
        $has = (bool) $this->cache->get($key);

        return $has;
    }

    public function Clear()
    {
        $this->cache->flush();
    }
}
