<?php
/**
 * APC cache adapter
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

class ApcAdapter extends BasicAdapter implements BasicInterface
{
    public $messagge = 'APC is not supported. Install it or use another cache adapter.';

    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function isSupported()
    {
        $isSupported = function_exists('apc_fetch') and ini_get('apc.enabled');

        return $isSupported;
    }

    public function Set($key, $value, $expiration)
    {
        $isSet = apc_store($key, serialize($value), $expiration);
        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Cache', array('type' => 'write', 'key' => $key));
        }

        return $isSet;
    }

    public function Get($key)
    {
        if (apc_exists($key)) {
            $value = unserialize(apc_fetch($key));
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
        if (apc_exists($key)) {
            $isDeleted = apc_delete($key);
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
        $has = apc_exists($key);

        return $has;
    }

    public function Clear()
    {
        foreach (new \APCIterator () as $item) {
            $this->Delete($item[key]);
        }
    }
}