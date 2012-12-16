<?php

/**
 * Language class
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

/**
 * Language class
 *
 * @package Luki
 */
class Luki_Language {

	private $sLanguagesPath = NULL;
	private $aLanguages = array();

	public function __construct($sName, $sFile)
	{
		$this->sLanguagesPath = dirname($sFile);

		$this->addToLanguages($sName, $sFile);

		unset($sName, $sFile);
	}

	public function Get($sText, $sName = '', $sSection = '')
	{
		$sTranslation = NULL;

		if(!empty($sName)) {
			$sTranslation = $this->aLanguages[$sName]->getValue($sText, $sSection);
		}
		else {
			foreach ($this->aLanguages as $oLanguage) {
				$sTranslation = $oLanguage->getValue($sText, $sSection);

				if(!empty($sTranslation)) {
					break;
				}
			}
		}

		unset($sText, $sName, $sSection, $oLanguage);
		return $sTranslation;
	}

	public function Find($sText)
	{
		$sTranslation = NULL;

		foreach ($this->aLanguages as $oLanguage) {
			$aSections = $oLanguage->getSections();

			foreach ($aSections as $sSection) {
				$sTranslation = $oLanguage->getValue($sText, $sSection);

				if(!empty($sTranslation)) {
					break;
				}
			}

			if(!empty($sTranslation)) {
				break;
			}
		}

		unset($sText, $oLanguage, $aSections, $sSection);
		return $sTranslation;
	}

	public function addToLanguages($sName, $sFile)
	{
		if(is_file($sFile)) {
			$this->aLanguages[$sName] = new Luki_Config($sFile);
		}
		elseif(is_file($this->sLanguagesPath . PATH_SEPARATOR . $sFile)) {
			$this->aLanguages[$sName] = new Luki_Config($this->sLanguagesPath . PATH_SEPARATOR . $sFile);
		}

		unset($sName, $sFile);
	}

	public function getSection($sName, $sSection = '')
	{
		$aSection = $this->aLanguages[$sName]->getSection($sSection);

		unset($sName, $sSection);
		return $aSection;
	}

	public function setSection($sName, $sSection)
	{
		$bReturn = $this->aLanguages[$sName]->setDefaultSection($sSection);

		unset($sName, $sSection);
		return $bReturn;
	}

	public function getSections($sName)
	{
		$aSections = $this->aLanguages[$sName]->getSections();

		unset($sName);
		return $aSections;
	}

	public function getPath()
	{
		return $this->sLanguagesPath;
	}

	public function setPath($sPath)
	{
		$sOldPath = $this->sLanguagesPath;

		if(is_dir($sPath)) {
			$this->sLanguagesPath = $sPath;
		}
		else {
			$sOldPath = FALSE;
		}

		unset($sPath);
		return $sOldPath;
	}

	public static function getPreferdLanguage()
	{
		$aLanguages = self::getAllowedLanguages();

		foreach ($aLanguages as $sPrefered => $sVersion) {
			if(!empty($sPrefered)) {
				break;
			}
		}

		unset($aLanguages, $sVersion);
		return $sPrefered;
	}

	public static function getAllowedLanguages()
	{
		$aLangs = array();

		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $aParsed);

			if(!empty($aParsed[1]) and !empty($aParsed[4])) {
				$aLangs = array_combine($aParsed[1], $aParsed[4]);

				foreach ($aLangs as $sLanguage => $sVersion) {
					if(empty($sVersion) or ' ' == $sVersion) {
						$aLangs[$sLanguage] = '1.0';
					}
				}

				arsort($aLangs, SORT_NUMERIC);
			}
		}

		unset($aParsed, $sLanguage, $sVersion);
		return $aLangs;
	}

}

# End of file