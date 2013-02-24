<?php

/**
 * Config xml adapter
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

/**
 * Config xml adapter
 * 
 * @package Luki
 */
class Luki_Config_xmlAdapter implements Luki_Config_Interface {

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

			libxml_use_internal_errors(TRUE);
			$oXML = simplexml_load_file($this->sFileName, 'SimpleXMLElement', LIBXML_NOERROR);
			$this->aConfiguration = json_decode(json_encode($oXML), TRUE);

			unset($oXML);
		}

		unset($sFileName);
	}

	/**
	 * Get configuration
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->aConfiguration;
	}

	/**
	 * Save configuration to file
	 * 
	 * @return boolean
	 */
	public function saveConfiguration()
	{
		$bReturn = FALSE;

		$oConfiguration = new DOMDocument('1.0', 'UTF-8');
		$oConfiguration->preserveWhiteSpace = false;
		$oConfiguration->formatOutput = true;
		$oElement = $oConfiguration->createElement('configuration');
		$oConfiguration->appendChild($oElement);

		foreach ($this->aConfiguration as $sSection => $aSectionValues) {
			$oSection = $oConfiguration->createElement($sSection);
			$oConfiguration->documentElement->appendChild($oSection);

			foreach ($aSectionValues as $sKey => $sValue) {
				$oKey = $oConfiguration->createElement($sKey, $sValue);
				$oSection->appendChild($oKey);
			}

			$sOutput = $oConfiguration->saveXML();
		}

		if(file_put_contents($this->sFileName, $sOutput) !== FALSE) {
			$bReturn = TRUE;
		}

		unset($oConfiguration, $oElement, $sSection, $aSectionValues, $oSection, $sKey, $sValue, $oKey, $sOutput);
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