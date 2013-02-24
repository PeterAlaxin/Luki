<?php

/**
 * Template class
 *
 * Luki framework
 * Date 6.1.2013
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
 * Template class
 *
 * @package Luki
 */
class Luki_Template {
	# Variables

	private $TemplateFile = '';
	private $sTemplate = '';
	private $oBlock;
	private $sBlockName = '';

	# Block
	private $start_block = "{start:";
	private $end_block = "{end:";
	private $end_block_tag = "}";

	# Include
	private $start_include = "{include:";
	private $end_include = "}";

	/**
	 * Constructor
	 *
	 * @param string $sFileName Template file with path
	 * @uses Template::_loadConfiguration() Load configuration
	 * @uses Template::_loadTemplate() Load template
	 * @uses Template::_deleteMemo() Delete memos from template
	 * @uses Template::_explodeTemplate() Explode template
	 * @uses Template::_transformConstants() Transform constants
	 */
	function __construct($sFileName)
	{
		if($this->_loadTemplate($sFileName)) {
			$this->sTemplate = $this->_deleteMemo();
			$this->_addIncluded();

			$this->oBlock = new Luki_Template_Block($this->sTemplate);
			$this->sBlockName = $this->oBlock->getName();
		}

		unset($sFileName);
	}

	public function Assign($sVariable, $xValue = NULL)
	{
		$this->oBlock->Assign($sVariable, $xValue);

		unset($sVariable, $xValue);
		return $this;
	}

	public function Parse($sBlock)
	{
		$sBlock = preg_replace('/' . $this->sBlockName . '\.?/', '', $sBlock);	
		$this->oBlock->Parse($sBlock);

		unset($sBlock);
		return $this;
	}

	/**
	 * Render block
	 *
	 * @param string $sBlock Block name
	 * @uses Template::Parse() Parse last block
	 */
	public function Render($sBlock)
	{
		$this->Parse($sBlock);
		$sBlock = $this->oBlock->Render();

		return $sBlock;
	}

	public function makeFormular($sFormular = '', $oFormular)
	{
		return $this;
	}

	/**
	 * Load template
	 *
	 * @access private
	 * @param string $sFileName Template file with path
	 * @uses Loader::canReadFile() Test temlpate file
	 */
	private function _loadTemplate($sFileName)
	{
		$bReturn = FALSE;

		if(is_file($sFileName)) {
			$this->TemplateFile = $sFileName;
			$this->sTemplate = file_get_contents($sFileName);
			$bReturn = TRUE;
		}
		
		unset($sFileName);
		return $bReturn;
	}

	/**
	 * Delete memos from template
	 */
	private function _deleteMemo($sString = '')
	{
		if(empty($sString)) {
			$sString = $this->sTemplate;
		}

		preg_match_all('/(\/\*).(.*)(\*\/)/isU', $sString, $aMatches, PREG_SET_ORDER);
		foreach ($aMatches as $aMatch) {
			$sString = str_replace($aMatch[0], '', $sString);
		}

		unset($aMatches, $aMatch);
		return $sString;
	}

	/**
	 * Add included templates
	 */
	private function _addIncluded()
	{
		while (TRUE) {
			preg_match_all('/(' . $this->start_include . ')(.*?)(' . $this->end_include . ')/', $this->sTemplate, $aMatches, PREG_SET_ORDER);

			if(0 == count($aMatches)) {
				break;
			}

			foreach ($aMatches as $aMatch) {

				if(strpos($aMatch[2], '/') === FALSE) {
					$aPath = explode('/', $this->TemplateFile);
					$aPath[count($aPath) - 1] = $aMatch[2];
					$sIncludedFile = implode('/', $aPath);
				}
				else {
					$aPath = explode('/', $aMatch[2]);
					foreach ($aPath as $nKey => $sDirectory) {
						if(defined($sDirectory)) {
							$sTransform = constant($sDirectory);
							if('/' == substr($sTransform, -1)) {
								$sTransform = substr($sTransform, 0, strlen($sTransform) - 1);
							}
							$aPath[$nKey] = $sTransform;
						}
					}
					$sIncludedFile = implode('/', $aPath);

					unset($nKey, $sDirectory, $sTransform);
				}

				unset($aPath);

				if(is_file($sIncludedFile)) {
					$sIncludedTemplate = file_get_contents($sIncludedFile);
					$sIncludedTemplate = preg_replace('/' . $this->start_block . 'main' . $this->end_block_tag . '/', '', $sIncludedTemplate);
					$sIncludedTemplate = preg_replace('/' . $this->end_block . 'main' . $this->end_block_tag . '/', '', $sIncludedTemplate);
					$sIncludedTemplate = $this->_deleteMemo($sIncludedTemplate);
					$this->sTemplate = str_replace($aMatch[0], $sIncludedTemplate, $this->sTemplate);
					unset($sIncludedTemplate);
				}
				unset($sIncludedFile);
			}
			unset($aMatch);
		}
		unset($aMatches);
	}

}

# End of file