<?php

/**
 * Config ini adapter
 *
 * Luki framework
 * Date 19.9.2012
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

namespace Luki\Config;

use Luki\Config\basicAdapter;

/**
 * Config ini adapter
 * 
 * @package Luki
 */
class iniAdapter extends basicAdapter {

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
        parent::__construct($sFileName);
        
		$this->aConfiguration = parse_ini_file($this->sFileName, TRUE);

		unset($sFileName);
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
		$sOutput = '';
		foreach ($this->aConfiguration as $sSection => $aSectionValues) {
			$sOutput .= '[' . $sSection . ']' . chr(10);
			foreach ($aSectionValues as $sKey => $sValue) {
				$sOutput .= $sKey . ' = "' . $sValue . '"' . chr(10);
			}
			$sOutput .= chr(10);
		}

        $bReturn = $this->saveToFile($sOutput);

		unset($sOutput, $sSection, $aSectionValues, $sKey, $sValue);
		return $bReturn;
	}

}

# End of file