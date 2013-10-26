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

namespace Luki;

use Luki\Config\basicInterface;

/**
 * Config class
 *
 * Load configuration
 *
 * @package Luki
 */
class Config {

	/**
	 * Search path array 
	 * @var array
	 * @access private
	 */
	private $Configuration = array();

	/**
	 * Configuration adapter
	 * @var object 
	 * @access private
	 */
	private $ConfigurationAdapter = NULL;

	/**
	 * Default section
	 * @var string
	 * @access private
	 */
	private $DefaultSection = '';

	/**
	 * All sections
	 * @var array
	 * @access private
	 */
	private $Sections = array();

	/**
	 * Constructor
	 */
	public function __construct(basicInterface $ConfigurationAdapter)
	{
		$this->ConfigurationAdapter = $ConfigurationAdapter;
		$this->Configuration = $this->ConfigurationAdapter->getConfiguration();
		$this->Sections = $this->ConfigurationAdapter->getSections();

		if(isset($this->Sections[0])) {
			$this->DefaultSection = $this->Sections[0];
		}

		unset($ConfigurationAdapter);
	}

    public static function findAdapter($File)
    {
        $FileInfo = pathinfo($File);
        $Adapter = __NAMESPACE__ . '\Config\\' . $FileInfo['extension'] . 'Adapter';
        
        unset($FileInfo);
        return $Adapter;
    }

	/**
	 * Get actual configuration
	 * @return array
	 */
	public function getConfiguration()
	{
		return $this->Configuration;
	}

	/**
	 * Get configuration filename
	 * @return string
	 */
	public function getConfigurationFile()
	{
		return $this->ConfigurationAdapter->getFilename();
	}

	/**
	 * Add new section
	 * @param type $Section Section name
	 * @param type $Values Array with values
	 * @return boolean
	 */
	public function addSection($Section, $Values = array())
	{
		$isAdded = FALSE;

		if(!empty($Section) and is_string($Section) and !in_array($Section, $this->Sections)) {
			$this->Configuration[$Section] = array();
			$this->Sections[] = $Section;
			$this->setDefaultSection($Section);
			$isAdded = TRUE;

			if(!empty($Values) and is_array($Values)) {
				$this->addValue($Values);
			}
		}

		unset($Section, $Values);
		return $isAdded;
	}

	/**
	 * Delete section
	 * @param type $Section Section name
	 * @return boolean
	 */
	public function deleteSection($Section)
	{
		$isDeleted = FALSE;
		$Section = $this->_fillEmptySection($Section);

		if(in_array($Section, $this->Sections)) {
			unset($this->Configuration[$Section]);
			$this->Sections = array_keys($this->Configuration);
			$isDeleted = TRUE;
		}

		unset($Section);
		return $isDeleted;
	}

	/**
	 * Get full section
	 * @param string $Section Section name
	 * @return array
	 */
	public function getSection($Section)
	{
		$Section = $this->_fillEmptySection($Section);
		$Values = array();

		if(in_array($Section, $this->Sections)) {
			$Values = $this->Configuration[$Section];
		}

		unset($Section);
		return $Values;
	}

	/**
	 * Get all sections
	 * @return array
	 */
	public function getSections()
	{
		return $this->Sections;
	}

	/**
	 * Add value to section
	 * @param type $Key Key of new value
	 * @param type $Value New value
	 * @param type $Section Section name
	 * @return boolean
	 */
	public function addValue($Key, $Value = '', $Section = '')
	{
		$isAdded = FALSE;

		if(!empty($Key)) {
			if(is_array($Key)) {
				$Values = $Key;
				$Section = $Value;
			}
			else {
				$Values = array($Key => $Value);
			}

			$Section = $this->_fillEmptySection($Section);
            $this->addSection($Section);
			
			foreach ($Values as $Key => $Value) {
				$this->Configuration[(string) $Section][(string) $Key] = (string) $Value;
			}
			$isAdded = TRUE;
		}

		unset($Key, $Value, $Section, $Values);
		return $isAdded;
	}

	/**
	 * Delete key from section
	 * @param type $Key Key from section
	 * @param type $Section Section name
	 * @return boolean
	 */
	public function deleteKey($Key, $Section = '')
	{
		$isDeleted = FALSE;

		$Section = $this->_fillEmptySection($Section);
		if(isset($this->Configuration[$Section][$Key])) {
			unset($this->Configuration[$Section][$Key]);
			$isDeleted = TRUE;
		}

		unset($Key, $Section);
		return $isDeleted;
	}

	/**
	 * Get value from configuration
	 * @param string $Key Key in section
	 * @param string $Section Section name
	 * @return string
	 */
	public function getValue($Key, $Section = '')
	{
		$Section = $this->_fillEmptySection($Section);
		$Value = NULL;

		if(isset($this->Configuration[$Section][$Key])) {
			$Value = $this->Configuration[$Section][$Key];
		}

		unset($Key, $Section);
		return $Value;
	}

	/**
	 * Set value
	 * @param type $Key Key in section
	 * @param type $Value New value
	 * @param type $Section Section name
	 * @return boolean
	 */
	public function setValue($Key, $Value = '', $Section = '')
	{
        $isSet = $this->addValue($Key, $Value, $Section);
        
        unset($Key, $Value, $Section);
        return $isSet;
    }

	/**
	 * Set section as default
	 * @param string $Section Section name
	 * @return boolean
	 */
	public function setDefaultSection($Section = '')
	{
		$isSet = FALSE;

		if(!empty($Section) and in_array($Section, $this->Sections)) {
			$this->DefaultSection = $Section;
			$isSet = TRUE;
		}

		unset($Section);
		return $isSet;
	}

	/**
	 * Get default section 
	 * 
	 * @return string
	 */
	public function getDefaultSection()
	{
		return $this->DefaultSection;
	}

	public function Save($File = '')
	{
		$isSaved = FALSE;

		if(!empty($File)) {
			$this->ConfigurationAdapter->setFilename($File);
		}

		if($this->ConfigurationAdapter->setConfiguration($this->Configuration)) {
			$isSaved = $this->ConfigurationAdapter->saveConfiguration();
		}

		unset($File);
		return $isSaved;
	}

	/**
	 * Fill empty section with default section 
	 * @param string $Section Section name
	 * @return string
	 * @access private
	 */
	private function _fillEmptySection($Section = '')
	{
		if(empty($Section) and !empty($this->DefaultSection)) {
			$Section = $this->DefaultSection;
		}

		return $Section;
	}

}

# End of file