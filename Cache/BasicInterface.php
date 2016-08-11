<?php
/**
 * Cache Adapter interface
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

interface BasicInterface
{

    public function __construct($options);

    public function __destruct();

    public function Set($key, $value, $expiration);

    public function Get($key);

    public function Delete($key);

    public function Has($key);

    public function Clear();

    public function isSupported();
    
    public function getExpiration();
}
