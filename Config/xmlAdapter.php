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

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName = '')
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
		$aConfiguration = array();

		if(!empty($this->sFileName)) {
			libxml_use_internal_errors(TRUE);
			$oXML = simplexml_load_file($this->sFileName, 'SimpleXMLElement', LIBXML_NOERROR);
			$aConfiguration = json_decode(json_encode($oXML), TRUE);

			unset($oXML);
		}

		return $aConfiguration;
	}

	/**
	 * Save configuration to specific file
	 * @param array $aConfiguration Configuration
	 * @param string $sFileName File to store configuration
	 * @return boolean
	 */
	public function saveConfiguration($aConfiguration, $sFileName = '')
	{
		$bReturn = FALSE;

		if(is_array($aConfiguration)) {
			if(empty($sFileName)) {
				$sFileName = $this->sFileName;
			}

			$oConfiguration = new DOMDocument('1.0', 'UTF-8');
			$oConfiguration->preserveWhiteSpace = false;
			$oConfiguration->formatOutput = true;
			$oElement = $oConfiguration->createElement('configuration');
			$oConfiguration->appendChild($oElement);

			foreach ($aConfiguration as $sSection => $aSectionValues) {
				$oSection = $oConfiguration->createElement($sSection);
				$oConfiguration->documentElement->appendChild($oSection);

				foreach ($aSectionValues as $sKey => $sValue) {
					$oKey = $oConfiguration->createElement($sKey, $sValue);
					$oSection->appendChild($oKey);
				}

				$sOutput = $oConfiguration->saveXML();
			}

			if(file_put_contents($sFileName, $sOutput) !== FALSE) {
				$bReturn = TRUE;
			}
		}

		unset($aConfiguration, $sFileName, $oConfiguration, $oElement, $sSection, $aSectionValues, $oSection, $sKey, $sValue, $oKey, $sOutput);
		return $bReturn;
	}

}

# End of file