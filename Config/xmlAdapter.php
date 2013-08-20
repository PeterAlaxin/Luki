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

namespace Luki\Config;

use Luki\Config\basicAdapter;

/**
 * Config xml adapter
 * 
 * @package Luki
 */
class xmlAdapter extends basicAdapter {

	/**
	 * Constructor
	 * @param type $sFileName
	 */
	public function __construct($sFileName)
	{
        parent::__construct($sFileName);
        
		libxml_use_internal_errors(TRUE);
		$oXML = simplexml_load_file($this->sFileName, 'SimpleXMLElement', LIBXML_NOERROR);
		$this->aConfiguration = json_decode(json_encode($oXML), TRUE);

		unset($sFileName, $oXML);
	}

	/**
	 * Save configuration to file
	 * 
	 * @return boolean
	 */
	public function saveConfiguration()
	{
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

        $bReturn = $this->saveToFile($sOutput);

		unset($oConfiguration, $oElement, $sSection, $aSectionValues, $oSection, $sKey, $sValue, $oKey, $sOutput);
		return $bReturn;
	}

}

# End of file