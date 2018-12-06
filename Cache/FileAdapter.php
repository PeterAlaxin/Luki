<?php
/**
 * File chache adapter
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

class FileAdapter extends BasicAdapter implements BasicInterface
{
    public $messagge = 'Path for cache is not writable.';
    private $prefix  = 'LukiFileCache_';

    public function __construct($options = array())
    {
        parent::__construct($options);
    }

    public function isSupported()
    {
        $isSupported = is_writable($this->path);

        return $isSupported;
    }

    public function Set($key, $value, $expiration)
    {
        $content = array('expiration' => $expiration, 'created' => time(), 'value' => $value);

        $isSet = (bool) file_put_contents($this->path.$this->prefix.$key, serialize($content), LOCK_EX);

        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Cache', array('type' => 'write', 'key' => $key));
        }

        return $isSet;
    }

    public function Get($key)
    {
        if (is_file($this->path.$this->prefix.$key)) {
            $content = unserialize(file_get_contents($this->path.$this->prefix.$key));
            if (!$this->isExpired($content)) {
                $value = $content['value'];
                if (Storage::isProfiler()) {
                    Storage::Profiler()->Add('Cache', array('type' => 'read', 'key' => $key));
                }
            } else {
                $this->Delete($key);
            }
        } else {
            $value = null;
        }

        return $value;
    }

    public function Delete($key)
    {
        if (is_file($this->path.$this->prefix.$key)) {
            $isDeleted = unlink($this->path.$this->prefix.$key);
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
        $has = is_file($this->path.$this->prefix.$key);

        return $has;
    }

    public function Clear()
    {
        $cacheFolder = dir($this->path);
        while (false !== ($file        = $cacheFolder->read())) {
            if (0 === strpos($file, $this->prefix)) {
                $this->Delete(str_replace($this->prefix, '', $file));
            }
        }
        $cacheFolder->close();
        unset($cacheFolder);
    }

    private function isExpired($content)
    {
        if ($content['expiration'] == 0 or time() > $content['created'] + $content['expiration']) {
            $isExpired = true;
        } else {
            $isExpired = false;
        }

        return $isExpired;
    }
}