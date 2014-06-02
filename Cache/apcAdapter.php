<?php

/**
 * APC chache adapter
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

namespace Luki\Cache;

use Luki\Cache\basicInterface;

/**
 * Memory chache adapter
 * 
 * @package Luki
 */
class apcAdapter implements basicInterface
{

    public function __construct($options = array())
    {
        unset($options);
    }

    public function Set($key, $value = '', $expirationInSeconds = 0)
    {
        $isSet = apc_store($key, serialize($value), $expirationInSeconds);

        unset($key, $value, $expirationInSeconds);
        return $isSet;
    }

    public function Get($key)
    {
        $value = unserialize(apc_fetch($key));

        unset($key);
        return $value;
    }

    public function Delete($key)
    {
        $isDeleted = apc_delete($key);

        unset($key);
        return $isDeleted;
    }

}

# End of file