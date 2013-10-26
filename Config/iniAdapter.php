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
	 * @param type $File
	 */
	public function __construct($File, $allowCreate = FALSE)
	{
        parent::__construct($File, $allowCreate);
        
		$this->Configuration = parse_ini_file($this->File, TRUE);

		unset($File, $allowCreate);
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
        parent::saveConfiguration();
        
		$OutputContent = '';
        
		foreach ($this->Configuration as $Section => $Values) {
			$OutputContent .= '[' . $Section . ']' . chr(10);

            foreach ($Values as $sKey => $sValue) {
				$OutputContent .= $sKey . ' = "' . $sValue . '"' . chr(10);
			}
			
            $OutputContent .= chr(10);
		}

        $isSaved = $this->saveToFile($OutputContent);

		unset($OutputContent, $Section, $Values, $sKey, $sValue);
		return $isSaved;
	}
}

# End of file