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
	 * Configuration file
	 * @var string
	 * @access private
	 */
	private $sFile = '';

	/**
	 * Default section
	 * @var string
	 * @access private
	 */
	private $sDefaultSection = '';

	/**
	 * All sections
	 * @var array
	 * @access private
	 */
	private $aSections = array();

	/**
	 * Constructor
	 */
	public function __construct($sConfigFile = '')
	{
		$sMimeType = Luki_File::getMimeType($sConfigFile);

		switch ($sMimeType) {

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
			$this->sFile = $sConfigFile;

			$this->aSections = array_keys($this->aConfiguration);
			if(isset($this->aSections[0])) {
				$this->sDefaultSection = $this->aSections[0];
			}
		}

		unset($sConfigFile, $sMimeType);
	}

	/**
	 * Get actual configuration
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->aConfiguration;
	}

	/**
	 * Get configuration filename
	 * @return string
	 */
	public function getConfigurationFile()
	{
		return $this->sFile;
	}

	/**
	 * Add new section
	 * @param type $sSection Section name
	 * @param type $aValues Array with values
	 * @return boolean
	 */
	public function addSection($sSection = '', $aValues = array())
	{
		$bReturn = FALSE;

		if(!empty($sSection) and is_string($sSection) and !in_array($sSection, $this->aSections)) {
			$this->aConfiguration[$sSection] = array();
			$this->aSections[] = $sSection;
			$this->setDefaultSection($sSection);
			$bReturn = TRUE;

			if(!empty($aValues) and is_array($aValues)) {
				$this->addValue($aValues);
			}
		}

		unset($sSection, $aValues);
		return $bReturn;
	}

	/**
	 * Delete section
	 * @param type $sSection Section name
	 * @return boolean
	 */
	public function deleteSection($sSection = '')
	{
		$bReturn = FALSE;
		$sSection = $this->_fillEmptySection($sSection);

		if(in_array($sSection, $this->aSections)) {
			unset($this->aConfiguration[$sSection]);
			unset($this->aSections[$sSection]);
			$bReturn = TRUE;
		}

		unset($sSection);
		return $bReturn;
	}

	/**
	 * Get full section
	 * @param string $sSection Section name
	 * @return array
	 */
	public function getSection($sSection = '')
	{
		$sSection = $this->_fillEmptySection($sSection);
		$aSection = array();

		if(in_array($sSection, $this->aSections)) {
			$aSection = $this->aConfiguration[$sSection];
		}

		unset($sSection);
		return $aSection;
	}

	/**
	 * Get all sections
	 * @return array
	 */
	public function getSections()
	{
		return $this->aSections;
	}

	/**
	 * Add value to section
	 * @param type $sKey Key of new value
	 * @param type $sValue New value
	 * @param type $sSection Section name
	 * @return boolean
	 */
	public function addValue($sKey = '', $sValue = '', $sSection = '')
	{
		$bReturn = FALSE;

		if(!empty($sKey)) {
			if(is_array($sKey)) {
				$aInsert = $sKey;
				$sSection = $sValue;
			}
			else {
				$aInsert = array($sKey => $sValue);
			}

			$sSection = $this->_fillEmptySection($sSection);

			if(in_array($sSection, $this->getSections())) {
				foreach ($aInsert as $sKey => $sValue) {
					$this->aConfiguration[(string) $sSection][(string) $sKey] = (string) $sValue;
				}
				$bReturn = TRUE;
			}
		}

		unset($sKey, $sValue, $sSection, $aInsert);
		return $bReturn;
	}

	/**
	 * Delete key from section
	 * @param type $sKey Key from section
	 * @param type $sSection Section name
	 * @return boolean
	 */
	public function deleteKey($sKey = '', $sSection = '')
	{
		$bReturn = FALSE;

		$sSection = $this->_fillEmptySection($sSection);
		if(isset($this->aConfiguration[$sSection][$sKey])) {
			unset($this->aConfiguration[$sSection][$sKey]);
			$bReturn = TRUE;
		}

		unset($sKey, $sSection);
		return $bReturn;
	}

	/**
	 * Get value from configuration
	 * @param string $sKey Key in section
	 * @param string $sSection Section name
	 * @return string
	 */
	public function getValue($sKey = '', $sSection = '')
	{
		$sSection = $this->_fillEmptySection($sSection);
		$xValue = NULL;

		if(isset($this->aConfiguration[$sSection][$sKey])) {
			$xValue = $this->aConfiguration[$sSection][$sKey];
		}

		unset($sKey, $sSection);
		return $xValue;
	}

	/**
	 * Set value
	 * @param type $sKey Key in section
	 * @param type $sValue New value
	 * @param type $sSection Section name
	 * @return boolean
	 */
	public function setValue($sKey = '', $sValue = '', $sSection = '')
	{
		$bReturn = FALSE;
		$sSection = $this->_fillEmptySection($sSection);

		if(!empty($sKey) and in_array($sSection, $this->getSections()) and isset($this->aConfiguration[(string) $sSection][(string) $sKey])) {
			$this->aConfiguration[(string) $sSection][(string) $sKey] = (string) $sValue;
			$bReturn = TRUE;
		}

		unset($sKey, $sValue, $sSection);
		return $bReturn;
	}

	/**
	 * Set section as default
	 * @param string $sSection Section name
	 * @return boolean
	 */
	public function setDefaultSection($sSection = '')
	{
		$bReturn = FALSE;

		if(!empty($sSection) and in_array($sSection, $this->aSections)) {
			$this->sDefaultSection = $sSection;
			$bReturn = TRUE;
		}

		unset($sSection);
		return $bReturn;
	}

	public function Save($sFileName = '')
	{
		if(empty($sFileName)) {
			$sFileName = $this->sFile;
		}
		$bReturn = $this->oConfigAdapter->saveConfiguration($this->aConfiguration, $sFileName);

		unset($sFileName);
		return $bReturn;
	}

	/**
	 * Fill empty section with default section 
	 * @param string $sSection Section name
	 * @return string
	 * @access private
	 */
	private function _fillEmptySection($sSection = '')
	{
		if(empty($sSection) and !empty($this->sDefaultSection)) {
			$sSection = $this->sDefaultSection;
		}

		return $sSection;
	}

}

# End of file