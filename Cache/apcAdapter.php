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
class apcAdapter implements basicInterface {

	public function __construct($Options = array())
	{
        unset($Options);
	}

	public function Set($Key, $Value = '', $ExpirationInSeconds = 0)
	{
		$isSet = apc_store($Key, serialize($Value), $ExpirationInSeconds);

		unset($Key, $Value, $ExpirationInSeconds);
		return $isSet;
	}

	public function Get($Key)
	{
		$Value = unserialize(apc_fetch($Key));

		unset($Key);
		return $Value;
	}

	public function Delete($Key)
	{
		$isDeleted = apc_delete($Key);

		unset($Key);
		return $isDeleted;
	}

}

# End of file