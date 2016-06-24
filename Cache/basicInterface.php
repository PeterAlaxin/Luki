<?php

/**
 * Cache Adapter interface
 *
 * Luki framework
 * Date 19.9.2012
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

/**
 * Cache Adapter interface
 * 
 * @package Luki
 */
interface basicInterface
{

    public function __construct($options);

    public function Set($key, $value, $expirationInSeconds);

    public function Get($key);

    public function Delete($key);
}

# End of file