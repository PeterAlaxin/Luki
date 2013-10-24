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

namespace Luki\Cache;

use Luki\Cache\basicInterface;

/**
 * File chache adapter
 * 
 * @package Luki
 */
class fileAdapter implements basicInterface {

	private $sPath;

	public function __construct($Options = array())
	{
		if(empty($Options) or !is_array($Options)) {
			$Options = array('path' => '/tmp/');
		}
		$this->sPath = $Options['path'];

		unset($Options);
	}

	public function Set($Key, $Value = '', $ExpirationInSeconds = 0)
	{
		$isSet = FALSE;
		$ValueContent = array('expiration' => $ExpirationInSeconds,
						'created' => time(),
						'value' => $Value);

		if(FALSE !== file_put_contents($this->sPath . $Key, serialize($ValueContent), LOCK_EX)) {
			$isSet = TRUE;			
		}

		unset($Key, $Value, $ExpirationInSeconds, $ValueContent);
		return $isSet;
	}

	public function Get($Key)
	{
		$Value = FALSE;

		if(is_file($this->sPath . $Key)) {
			$ValueContent = unserialize(file_get_contents($this->sPath . $Key));
			if(!$this->isExpired($ValueContent)) {
				$Value = $ValueContent['value'];
			}
            else {
                $this->Delete($Key);
            }
		}

		unset($Key, $ValueContent);
		return $Value;
	}

	public function Delete($Key)
	{
		$isDeleted = FALSE;

		if(is_file($this->sPath . $Key)) {
			$isDeleted = unlink($this->sPath . $Key);
		}

		unset($Key);
		return $isDeleted;
	}

    private function isExpired($ValueContent)
    {
        $isExpired = TRUE;
        
        if($ValueContent['expiration'] == 0 or time() < $ValueContent['created'] + $ValueContent['expiration']) {
            $isExpired = FALSE;
        }
        
        unset($ValueContent);
        return $isExpired;
    }
}

# End of file