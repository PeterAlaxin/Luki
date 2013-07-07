<?php

/**
 * Basic config adapter
 *
 * Luki framework
 * Date 7.7.2013
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
 * Basic config adapter
 * 
 * @package Luki
 */
class Luki_Config_basicAdapter implements Luki_Config_Interface {

    /**
     * File name
     * @var string
     */
	public $sFileName = '';
    
    /**
     * Configuration
     * @var array
     */
	public $aConfiguration = array();

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
		if(is_file($sFileName)) {
			$this->sFileName = $sFileName;
		}

		unset($sFileName);
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->aConfiguration;
	}

	/**
	 * Read configuration file
	 * @return array
	 */
	public function getSections()
	{
        $aSections = array_keys($this->aConfiguration);
        
		return $aSections;
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
	}

    /**
     * Get configuration file name
     * @return string
     */
	public function getFilename() {
		return $this->sFileName;		
	}

    /**
     * Change actual configuration
     * @param array $aConfiguration
     * @return boolean
     */
	public function setConfiguration($aConfiguration)
	{
		$bReturn = FALSE;
		if(is_array($aConfiguration)) {
			$this->aConfiguration = $aConfiguration;
			$bReturn = TRUE;
		}

		unset($aConfiguration);
		return $bReturn;		
	}

    /**
     * Set file name
     * @param string $sFileName
     * @return boolean
     */
	public function setFilename($sFileName)
	{
		$bReturn = FALSE;
		if(!empty($sFileName)) {
			$this->sFileName = $sFileName;
			$bReturn = TRUE;
		}

		unset($sFileName);
		return $bReturn;		
	}

}

# End of file