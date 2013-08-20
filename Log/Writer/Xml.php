<?php

/**
 * Xml Log Writer
 *
 * Luki framework
 * Date 16.12.2012
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

namespace Luki\Log\Writer;

use Luki\Log\Writer\basicInterface;

/**
 * Xml Log Writer
 * 
 * @package Luki
 */
class Xml implements basicInterface {

	private $sFile = NULL;

	private $oFile = NULL;
	
	public function __construct($sFile='')
	{
		$this->sFile = $sFile;
		
		if(is_file($sFile)) {
			$this->oFile = new \SimpleXMLElement($sFile, LIBXML_NOERROR, TRUE);
		}
		else {
			$sFile = '<?xml version="1.0" encoding="UTF-8"?><items></items>';
			$this->oFile = new \SimpleXMLElement($sFile);			
		}
		
		unset($sFile);
	}
	
	public function __destruct()
	{
		file_put_contents($this->sFile, $this->oFile->asXML());
	}
	
	public function Write($sText)
	{
		if(is_array($sText)) {
			$oItem = $this->oFile->addChild('item');

			foreach($sText as $sKey => $sValue) {
				$oItem->addChild($sKey, $sValue);
			}
		}
		else {
			$this->oFile->addChild('item', $sText);
		}
			
		unset($sText, $sKey, $sValue);
	}
}

# End of file