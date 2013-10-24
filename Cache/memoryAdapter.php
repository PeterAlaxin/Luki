<?php

/**
 * Memory chache adapter
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
class memoryAdapter implements basicInterface {

	private $oMemcache;

	public function __construct($Options = array())
	{
		if(empty($Options) or !is_array($Options)) {
			$Options = array(
				'server' => 'localhost',
				'port' => '11211');
		}
		$this->oMemcache = new \Memcache;
		$this->oMemcache->connect($Options['server'], $Options['port']);

		unset($Options);
	}

	public function Set($Key, $Value = '', $ExpirationInSeconds = 0)
	{
		$isSet = $this->oMemcache->set($Key, serialize($Value), MEMCACHE_COMPRESSED, $ExpirationInSeconds);

		unset($Key, $Value, $ExpirationInSeconds);
		return $isSet;
	}

	public function Get($Key)
	{
		$sValue = unserialize($this->oMemcache->get($Key, MEMCACHE_COMPRESSED));

		unset($Key);
		return $sValue;
	}

	public function Delete($Key)
	{
		$isDeleted = $this->oMemcache->delete($Key);

		unset($Key);
		return $isDeleted;
	}

}

# End of file