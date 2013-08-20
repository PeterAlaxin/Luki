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

	public function __construct($aOptions = array())
	{
        unset($aOptions);
	}

	public function Set($sKey, $sValue = '', $nExpire = 0)
	{
		$sValue = serialize($sValue);
        
		$bReturn = apc_store($sKey, $sValue, $nExpire);

		unset($sKey, $sValue, $nExpire);
		return $bReturn;
	}

	public function Get($sKey)
	{
		$sValue = apc_fetch($sKey);
		$sReturn = unserialize($sValue);

		unset($sKey, $sValue);
		return $sReturn;
	}

	public function Delete($sKey)
	{
		$bReturn = apc_delete($sKey);

		unset($sKey);
		return $bReturn;
	}

}

# End of file