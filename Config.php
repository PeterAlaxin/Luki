<?php
/**
 * Config class
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
 * Config class
 *
 * Load configuration
 *
 * @package Luki
 */
class Luki_Config {

	/**
	 * Search path array 
	 * @var array
	 * @access private
	 */
	private $aConfiguration = array();

	/**
	 * Configuration adapter
	 * @var object 
	 * @access private
	 */
	private $oConfigAdapter = NULL;
	
	/**
	 * Basic constructor
	 */
	public function __construct($sConfigFile='')
	{	
		$sMimeType = Luki_File::getMimeType($sConfigFile);

		switch($sMimeType) {
			
			# XML configuration
			case 'application/xml':
				$this->oConfigAdapter = new Luki_Config_xmlAdapter($sConfigFile);
				break;
			
			# INI configuration
			case 'text/x-pascal':
				$this->oConfigAdapter = new Luki_Config_iniAdapter($sConfigFile);
				break;
			
			# Wrong file
			default:
		}
		
		if(is_object($this->oConfigAdapter) and is_a($this->oConfigAdapter, 'Luki_Config_Interface')) {
			$this->aConfiguration = $this->oConfigAdapter->getConfiguration();
		}
	}
	
	public function getConfiguration()
	{
		$aConfiguration = $this->oConfigAdapter->getConfiguration();
		
		return $aConfiguration;
	}

	public function getConfigurationFile()
	{
		$sFile = $this->oConfigAdapter->getConfigurationFile();
		
		return $sFile;
	}
	
	public function getSection($sSection='')
	{
		$aSection = $this->oConfigAdapter->getSection($sSection);		
		unset($sSection);
		
		return $aSection;
	}
	
	public function getSections()
	{
		$aSections = $this->oConfigAdapter->getSections();		
		
		return $aSections;
	}
	
	public function getValue($sKey='', $sSection='')
	{
		$xValue = $this->oConfigAdapter->getValue($sKey, $sSection);		
		unset($sKey, $sSection);
		
		return $xValue;
		
	}
	
	public function setDefaultSection($sSection='')
	{
		$this->oConfigAdapter->setDefaultSection($sSection);
	}	
}

# End of file