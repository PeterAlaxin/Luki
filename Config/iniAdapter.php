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

/**
 * Config ini adapter
 * 
 * @package Luki
 */
class Luki_Config_iniAdapter implements Luki_Config_Interface {

	private $aConfiguration = array();
	
	private $sFile = '';
	
	private $sDefaultSection = '';
	
	private $aSections = array();
	
	public function __construct($sFileName='')
	{
		if(is_file($sFileName)) {
			$this->sFile = $sFileName;
			$this->aConfiguration =  parse_ini_file($sFileName, TRUE);
			$this->aSections = array_keys($this->aConfiguration);
			
			if(isset($this->aSections[0])) {
				$this->sDefaultSection = $this->aSections[0];
			}
		}
		
		unset($sFileName);
	}
	
	public function getConfiguration()
	{	
		return $this->aConfiguration;
	}

	public function getConfigurationFile() 
	{
		return $this->sFile;
	}	
	
	public function getSection($sSection='')
	{
		$sSection = $this->_fillEmptySection($sSection);
		
		$aSection = array();
		if(in_array($sSection, $this->aSections)) {
			$aSection = $this->aConfiguration[$sSection];
		}
		
		unset($sSection);
		
		return $aSection;
	}
	
	public function getSections()
	{
		return $this->aSections;
	}

	public function getValue($sKey='', $sSection='')
	{
		$sSection = $this->_fillEmptySection($sSection);

		$xValue = NULL;
		if(isset($this->aConfiguration[$sSection][$sKey])) {
			$xValue = $this->aConfiguration[$sSection][$sKey];
		}
			
		unset($sKey, $sSection);
		
		return $xValue;
	}

	public function setDefaultSection($sSection='')
	{
		$bReturn = FALSE;
		
		if(!empty($sSection) and in_array($sSection, $this->aSections)) {
			$this->sDefaultSection = $sSection;
			$bReturn = TRUE;
		}
		
		unset($sSection);
		
		return $bReturn;
	}

	private function _fillEmptySection($sSection='')
	{
		if(empty($sSection) and !empty($this->sDefaultSection)) {
			$sSection = $this->sDefaultSection;
		}
		
		return $sSection;
	}
}

# End of file