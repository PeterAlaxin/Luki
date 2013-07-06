<?php

/**
 * Config yaml adapter
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

/**
 * Config yaml adapter
 * 
 * @package Luki
 */
class Luki_Config_yamlAdapter implements Luki_Config_Interface {

	private $sFileName = '';
	private $aConfiguration = array();

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
		if(is_file($sFileName)) {
			$this->sFileName = $sFileName;
            $sConfigContent = file_get_contents($this->sFileName);
			$this->aConfiguration = yaml_parse($sConfigContent);
		}

		unset($sFileName, $sConfigContent);
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
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration()
	{
		$bReturn = FALSE;

		$sOutput = yaml_emit($this->aConfiguration);

		if(file_put_contents($this->sFileName, $sOutput) !== FALSE) {
			$bReturn = TRUE;
		}

		unset($sOutput);
		return $bReturn;
	}

	public function getFilename() {
		return $this->sFileName;		
	}

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