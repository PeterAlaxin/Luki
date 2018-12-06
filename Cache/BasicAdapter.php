<?php
/**
 * Basic cache adapter
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

use Luki\Exception\CacheException;

abstract class BasicAdapter
{
    public $path       = '/tmp/';
    public $expiration = 600;
    public $server     = 'localhost';
    public $port       = 11211;

    public function __construct($options)
    {
        if (!is_array($options)) {
            throw new CacheException('Cache options is not array!');
        }

        $this->fillOptions($options);

        if (!$this->isSupported()) {
            throw new CacheException($this->messagge);
        }
    }

    private function fillOptions($options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'path':
                    if ('/' !== substr($value, -1)) {
                        $value .= '/';
                    }
                    $this->path       = $value;
                    break;
                case 'server':
                    $this->server     = $value;
                    break;
                case 'port':
                    $this->port       = $value;
                    break;
                case 'expiration':
                    $this->expiration = $value;
                    break;
            }
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getExpiration()
    {
        return $this->expiration;
    }
}