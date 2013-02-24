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

/**
 * Memory chache adapter
 * 
 * @package Luki
 */
class Luki_Cache_memoryAdapter implements Luki_Cache_Interface {

	private $oMemcache;

	public function __construct($aOptions = array())
	{
		if(empty($aOptions) or !is_array($aOptions)) {
			$aOptions = array(
				'server' => 'localhost',
				'port' => '11211');
		}
		$this->oMemcache = new Memcache;
		$this->oMemcache->connect($aOptions['server'], $aOptions['port']);

		unset($aOptions);
	}

	public function Set($sKey, $sValue = '', $nExpire = 0)
	{
		$sValue = serialize($sValue);
		$bReturn = $this->oMemcache->set($sKey, $sValue, MEMCACHE_COMPRESSED, $nExpire);

		unset($sKey, $sValue, $nExpire);
		return $bReturn;
	}

	public function Get($sKey)
	{
		$sValue = $this->oMemcache->get($sKey, MEMCACHE_COMPRESSED);
		$sReturn = unserialize($sValue);

		unset($sKey, $sValue);
		return $sReturn;
	}

	public function Delete($sKey)
	{
		$bReturn = $this->oMemcache->delete($sKey);

		unset($sKey);
		return $bReturn;
	}

}

# End of file