<?php

/**
 * Config yml adapter
 *
 * Luki framework
 * Date 6.7.2013
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
 * Config yml adapter
 * 
 * @package Luki
 */
class ymlAdapter extends basicAdapter {

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
        parent::__construct($sFileName);
        
        $sConfigContent = file_get_contents($this->sFileName);
        $this->aConfiguration = yaml_parse($sConfigContent);
 
		unset($sFileName, $sConfigContent);
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
		$sOutput = yaml_emit($this->aConfiguration);

        $bReturn = $this->saveToFile($sOutput);

		unset($sOutput);
		return $bReturn;
	}

}

# End of file