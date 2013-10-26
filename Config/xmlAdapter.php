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
	 * @param type $File
	 */
	public function __construct($File, $allowCreate = FALSE)
	{
        parent::__construct($File, $allowCreate);
        
		libxml_use_internal_errors(TRUE);
		$XML = simplexml_load_file($this->File, 'SimpleXMLElement', LIBXML_NOERROR);
		$this->Configuration = json_decode(json_encode($XML), TRUE);

		unset($File, $XML, $allowCreate);
	}

	/**
	 * Save configuration to file
	 * 
	 * @return boolean
	 */
	public function saveConfiguration()
	{
        parent::saveConfiguration();
        
		$OutputContent = new DOMDocument('1.0', 'UTF-8');
		$OutputContent->preserveWhiteSpace = false;
		$OutputContent->formatOutput = true;
		$Element = $OutputContent->createElement('configuration');
		$OutputContent->appendChild($Element);

		foreach ($this->Configuration as $Section => $Values) {
			$NewSection = $OutputContent->createElement($Section);
			$OutputContent->documentElement->appendChild($NewSection);

			foreach ($Values as $Key => $Value) {
				$NewSection->appendChild($OutputContent->createElement($Key, $Value));
			}
		}
        $isSaved = $this->saveToFile($OutputContent->saveXML());

		unset($OutputContent, $Element, $Section, $Values, $NewSection, $Key, $Value);
		return $isSaved;
	}

}

# End of file