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

	const EOL = "\n";
	const EOL2 = "\n\n";
	const TAB = "\t";
	const TAB2 = "\t\t";

	protected $sTwigPath = '/var/projects/demo/data/';
	protected $sTemplate = '';
	protected $sClass = '';
	protected $aData = array();
	protected $aVariables = array();
	protected $aBlocks = array();
	protected $sTwig = '';
	protected $sNewClass = '';
	protected $aFilters = array();
	protected $aFunctions = array();

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
	function __construct($sTemplate, $aData)
	{
		$this->sTemplate = $sTemplate;
		$this->aData = (array) $aData;

		$sTemplate = preg_replace('/.twig/', '', $sTemplate);
		$sTemplate = preg_replace('/\/template\//', '', $sTemplate);
		$sClass = ucwords(preg_replace('/\//', ' ', $sTemplate));
		$this->sClass = 'twig_' . implode('', array_slice(explode(' ', $sClass), -1));
		$this->sNewClass = $this->sTwigPath . preg_replace('/_/', '/', $this->sClass) . '.php';

		if(!file($this->sNewClass) or filectime($this->sTemplate) > filectime($this->sNewClass)) {
			$this->_generateTemplate();
		}

		unset($sTemplate, $aData, $sClass);
	}

	public function Render()
	{
		$oTemplateClass = new $this->sClass($this->aData);
		$oTemplateClass->Render();
	}

	private function _generateTemplate()
	{
		$this->sTwig = file_get_contents($this->sTemplate);

		$this->_clearComments();

		$sClass = $this->_begin();
		$sClass .= $this->_defineFilters();
		$sClass .= $this->_defineBlocks();
		$sClass .= $this->_defineFunctions();
		$sClass .= $this->_end();

		file_put_contents($this->sNewClass, $sClass);

		unset($sClass);
	}

	private function _clearComments()
	{
		preg_match_all('|{# (.*) #}|U', $this->sTwig, $aMatches, PREG_SET_ORDER);

		foreach ($aMatches as $aMatch) {
			$this->sTwig = str_replace($aMatch[0], '', $this->sTwig);
		}

		unset($aMatches, $aMatch);
	}

	private function _begin()
	{
		$sBegin = '<?php' . self::EOL;
		$sBegin .= 'class ' . $this->sClass . self::EOL;
		$sBegin .= '{' . self::EOL2;
		$sBegin .= self::TAB . 'protected $aFilters = array();' . self::EOL2;
		$sBegin .= self::TAB . 'protected $aData = array();' . self::EOL2;
		$sBegin .= self::TAB . 'public function __construct($aData)' . self::EOL;
		$sBegin .= self::TAB . '{' . self::EOL;
		$sBegin .= self::TAB2 . '$this->aData = $aData;' . self::EOL;
		$sBegin .= self::TAB2 . '$this->_defineFilters();' . self::EOL;
		$sBegin .= self::TAB . '}' . self::EOL2;
		$sBegin .= self::TAB . 'public function Render()' . self::EOL;
		$sBegin .= self::TAB . '{' . self::EOL;
		$sBegin .= self::TAB2 . 'echo $this->_mainBlock();' . self::EOL;
		$sBegin .= self::TAB . '}' . self::EOL2;

		return $sBegin;
	}

	private function _end()
	{
		$sEnd = '}';

		return $sEnd;
	}

	private function _defineFilters()
	{
		$sFilters = self::TAB . 'private function _defineFilters()' . self::EOL;
		$sFilters .= self::TAB . '{' . self::EOL;

		$aFiles = Luki_File::getFilesInDirectory(__DIR__ . '/Template/Filter');

		foreach ($aFiles as $sFile) {
			$sFile = preg_replace('/.php/', '', $sFile);
			$sFilter = strtolower($sFile);
			$sFilters .= self::TAB2 . '$this->aFilters["' . $sFilter . '"] = new Luki_Template_Filter_' . $sFile . ';' . self::EOL;
			$this->aFilters[] = $sFilter;
		}
		$sFilters .= self::TAB . '}' . self::EOL2;

		unset($aFiles, $sFile, $sFilter);
		return $sFilters;
	}

	private function _defineBlocks()
	{
		$sMainBlock = $this->_parseBlocks($this->sTwig);
		$this->aBlocks['main'] = new Luki_Template_Block($sMainBlock);

		$sBlocks = '';
		foreach ($this->aBlocks as $sName => $oBlock) {
			$sBlocks .= self::TAB . 'private function _' . $sName . 'Block()' . self::EOL;
			$sBlocks .= self::TAB . '{' . self::EOL;
			$sBlocks .= self::TAB2 . ' ?>' . $oBlock->getContent() . '<?php ' . self::EOL;
			$sBlocks .= self::TAB . '}' . self::EOL2;
			
			$this->aVariables = array_merge($this->aVariables, $oBlock->getVariables());
		}

		unset($sName, $oBlock, $sMainBlock);
		return $sBlocks;
	}

	private function _parseBlocks($sBlock)
	{
		preg_match_all('|({% block (.*) %})|U', $sBlock, $aStartMatches, PREG_SET_ORDER);
		preg_match_all('|({% endblock(.*) %})|U', $sBlock, $aEndMatches, PREG_SET_ORDER);
		
		if(count($aStartMatches) != count($aEndMatches)) {
			echo 'Template error';
			exit;
		}

		while(count($aStartMatches) > 0) {
			foreach($aStartMatches as $aBlock) {
				preg_match_all('|({% block ' . $aBlock[2] . ' %})([\s\S]*)({% endblock(.*) %})|U', $sBlock, $aBlockMatches, PREG_SET_ORDER);
		
				foreach($aBlockMatches as $aSubBlock) {
					if(0 === preg_match_all('/{% block (.*) %}/', $aSubBlock[2])) {
						$this->aBlocks[$aBlock[2]] = new Luki_Template_Block($aSubBlock);
						$sBlock = str_replace( $aSubBlock[0], '<?php $this->_' . $aBlock[2] . 'Block(); ?>', $sBlock);
					}
				}
			}
	
			preg_match_all('|({% block (.*) %})|U', $sBlock, $aStartMatches, PREG_SET_ORDER);
		}

		return $sBlock;
	}
	
	private function _defineFunctions()
	{
		$sFunctions = '';

		foreach ($this->aVariables as $oVariable) {
			
			$sFunctionName = $oVariable->getFunctionName();

			if(!empty($sFunctionName) and !in_array($sFunctionName, $this->aFunctions)) {
				$sFunctions .= $oVariable->getFunction();
				$this->aFunctions[] = $sFunctionName;
			}
		}

		unset($oVariable, $sFunctionName);
		return $sFunctions;
	}

}

# End of file