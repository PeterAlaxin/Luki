<?php

/**
 * File chache adapter
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
 * File chache adapter
 * 
 * @package Luki
 */
class Luki_Cache_fileAdapter implements Luki_Cache_Interface {

	private $sPath;

	public function __construct($aOptions = array())
	{
		if(empty($aOptions) or !is_array($aOptions)) {
			$aOptions = array('path' => '/tmp/');
		}
		$this->sPath = $aOptions['path'];

		unset($aOptions);
	}

	public function Set($sKey, $sValue = '', $nExpire = 0)
	{
		$bReturn = FALSE;
		$aValue = array('expiration' => $nExpire,
						'created' => time(),
						'value' => $sValue);

		$xReturn = file_put_contents($this->sPath . $sKey, serialize($aValue), LOCK_EX);
		if(FALSE !== $xReturn) {
			$bReturn = TRUE;			
		}

		unset($sKey, $sValue, $nExpire, $aValue, $xReturn);
		return $bReturn;
	}

	public function Get($sKey)
	{
		$xReturn = FALSE;

		if(is_file($this->sPath . $sKey)) {
			$sContent = file_get_contents($this->sPath . $sKey);
			$aContent = unserialize($sContent);
			if($aContent['expiration'] == 0 or time() < $aContent['created'] + $aContent['expiration']) {
				$xReturn = $aContent['value'];
			}
		}

		unset($sKey, $sContent, $aContent);
		return $xReturn;
	}

	public function Delete($sKey)
	{
		$bReturn = FALSE;

		if(is_file($this->sPath . $sKey)) {
			$bReturn = unlink($this->sPath . $sKey);
		}

		unset($sKey);
		return $bReturn;
	}

}

# End of file