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
	
	protected $sTwigPath = '/var/projects/demo/data/';
	protected $sTemplate = '';
	protected $sClass = '';
	protected $aData = array();
	protected $aVariables = array();
	protected $aBlocks = array();
	protected $sTwig = '';
	protected $sNewClass = '';
	protected $aFilters = array();
	
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
		$this->aData = (array)$aData;
				
		$sTemplate = preg_replace('/.twig/', '', $sTemplate);
		$sTemplate = preg_replace('/\/template\//', '', $sTemplate);
		$sClass = ucwords(preg_replace('/\//', ' ', $sTemplate));
		$this->sClass = 'twig_' . implode('', array_slice(explode(' ', $sClass), -1));
		$this->sNewClass = $this->sTwigPath . preg_replace('/_/', '/', $this->sClass) . '.php';
			
#		if(!file($this->sNewClass) or filectime($this->sTemplate) > filectime($this->sNewClass)) {
			$this->_generateTemplate();
#		}
		
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
		$this->_defineVariables();
		$sClass .= $this->_defineBlocks();		
		$sClass .= $this->_defineFunctions();
		$sClass .= $this->_end();
		
		file_put_contents($this->sNewClass , $sClass);
	}
	
	private function _clearComments()
	{
		preg_match_all('|{# (.*) #}|U', $this->sTwig, $aMatches, PREG_SET_ORDER);

		foreach($aMatches as $aMatch) {
			$this->sTwig = str_replace($aMatch[0], '', $this->sTwig);
		}
	}
	
	private function _begin()
	{
		$t = chr(9);
		$tt = chr(9) . chr(9);
		$n = chr(10);
		$nn = chr(10) . chr(10);
		
		$sBegin = '<?php' . $n;
		$sBegin .= 'class ' . $this->sClass . $n;
		$sBegin .= '{' . $nn;
		$sBegin .= $t . 'protected $aFilters = array();' . $nn;
		$sBegin .= $t . 'protected $aData = array();' . $nn;
		$sBegin .= $t . 'public function __construct($aData)' . $n;
		$sBegin .= $t . '{' . $n;
		$sBegin .= $tt . '$this->aData = $aData;' . $n;
		$sBegin .= $tt . '$this->_defineFilters();' . $n;
		$sBegin .= $t . '}' . $nn;
		$sBegin .= $t . 'public function Render()' . $n;
		$sBegin .= $t . '{' . $n;
		$sBegin .= $tt . 'echo $this->_mainBlock();' . $n;
		$sBegin .= $t . '}' . $nn;
		
		return $sBegin;
	}
	
	private function _end()
	{
		$sEnd = '}';
		
		return $sEnd;
	}
	
	private function _defineFilters() 
	{
		$t = chr(9);
		$tt = chr(9) . chr(9);
		$n = chr(10);
		$nn = chr(10) . chr(10);

		$sFilters = $t . 'private function _defineFilters()' . $n;
		$sFilters .= $t . '{' . $n;
		
		$aFiles = Luki_File::getFilesInDirectory(__DIR__ . '/Template/Filter');
		
		foreach($aFiles as $sFile) {
			$sFile = preg_replace('/.php/', '', $sFile);
			$sFilter = strtolower($sFile);
			$sFilters .= $tt . '$this->aFilters["' . $sFilter . '"] = new Luki_Template_Filter_' . $sFile . ';' . $n;
			$this->aFilters[] = $sFilter;
		}
		$sFilters .= $t . '}' . $nn;

		return $sFilters;
	}
	
	private function _defineBlocks()
	{
		$t = chr(9);
		$tt = chr(9) . chr(9);
		$n = chr(10);
		$nn = chr(10) . chr(10);

		$this->_explodeBlock('main', $this->sTwig);
		
		$sBlocks = '';		
		foreach($this->aBlocks as $sName => $aBlock)
		{
			$sBlocks .= $t . 'private function _' . $sName . 'Block()' . $n;
			$sBlocks .= $t . '{' . $n;
			$sBlocks .= $tt . $aBlock['code'] . $n;
			$sBlocks .= $t . '}' . $nn;
		}
		
		return $sBlocks;
	}
	
	private function _explodeBlock($sName, $sBlock)
	{
		$this->aBlocks[$sName] = array('source' => $sBlock);
		
		foreach($this->aVariables as $sVariable => $aVariable) {
			$sBlock = str_replace($sVariable, $aVariable['code'], $sBlock);
		}
		
		$this->aBlocks[$sName]['code'] = ' ?>' . $sBlock . '<?php ';
	}
	
	private function _defineFunctions() 
	{
		$sFunctions = '';
		
		foreach($this->aVariables as $aVariable) {
			if(!empty($aVariable['function'])) {
				$sFunctions .= $aVariable['function'];
			}
		}
		
		return $sFunctions;
	}
	
	private function _defineVariables()
	{
		preg_match_all('|{{ (.*) }}|U', $this->sTwig, $aMatches, PREG_SET_ORDER);
		
		foreach ($aMatches as $aVariable) {
			$this->_addVariable($aVariable[0], $aVariable[1]);
		}		
	}
	
	private function _addVariable($sName, $sContent)
	{
		if(!array_key_exists($sName, $this->aVariables)) {
			$this->aVariables[$sName] = array(
				'content' => $sContent,
				'name' => 'var' . Luki_Security::generatePassword(6, 3));

			$sVariable = $this->_addVariableFilters($sName);

			$this->_addVariableVariable($sName, $sVariable);
			
			$this->_addVariableCode($sName);
		}
	}

	private function _addVariableFilters($sName)
	{
		$aFilters = array();
		$sReturn = $this->aVariables[$sName]['content'];
		
		if(strpos($sReturn, '|')) {
			$aFilters = explode('|', $sReturn);
			$sReturn = array_shift($aFilters);
		}
		
		$this->aVariables[$sName]['filters'] = $aFilters;
				
		return $sReturn;
	}

	private function _addVariableVariable($sName, $sVariable)
	{
		# Array
		preg_match('/^[\[{](.*)[\]}]$/', $sVariable, $aMatches);
		if(count($aMatches) > 0) {
			$aNewItems = array();
			$aItems = explode(', ', $aMatches[1]);

			foreach($aItems as $Item) {
				if(strpos($Item, ': ')) {
					$aSubItems = explode(': ', $Item);	
					
					$aNewItems[] = $this->_stringToVariable($aSubItems[0]) . 
						' => ' . 
						$this->_stringToVariable($aSubItems[1]);
				}
				else {
					$aNewItems[] = $this->_stringToVariable($Item);
				}
			}

			$sVariable = preg_replace('/[\[{]/', 'array(', $sVariable);
			$sVariable = preg_replace('/' . $aMatches[1] . '/', implode(', ', $aNewItems), $sVariable);
			$sVariable = preg_replace('/[\]}]/', ')', $sVariable);
		}
		else {
			$sVariable = $this->_stringToVariable($sVariable);
		}
		
		$this->aVariables[$sName]['variable'] = $sVariable;
	}
	
	private function _addVariableCode($sName)
	{
		$sCode = '<?php echo ';
		if(!empty($this->aVariables[$sName]['filters'])) {
			$sCode .= '$this->_' . $this->aVariables[$sName]['name'] . '(' . $this->aVariables[$sName]['variable'] . ');';
			
			$this->_addVariableFunction($sName);
		}
		else {
			$sCode .= $this->aVariables[$sName]['variable'] . ';';
		}
		$sCode .= ' ?>';
		
		$this->aVariables[$sName]['code'] = $sCode;
	}
	
	private function _addVariableFunction($sName)
	{
		$t = chr(9);
		$tt = chr(9) . chr(9);
		$n = chr(10);
		$nn = chr(10) . chr(10);

		$sFunction = $t . 'private function _' . $this->aVariables[$sName]['name'] . '($xValue)' . $n;
		$sFunction .= $t . '{' . $n;
		foreach($this->aVariables[$sName]['filters'] as $sFilter) {
			
			preg_match_all('|(.*)\((.*)\)|U', $sFilter, $aMatches, PREG_SET_ORDER);

			if(empty($aMatches)) {
				if(!in_array($sFilter, $this->aFilters)) {
					continue;
				}
				$sFunction .= $tt . '$xValue = $this->aFilters["' . $sFilter . '"]->Get($xValue);' . $n;
			}
			else {
				if(!in_array($aMatches[0][1], $this->aFilters)) {
					continue;
				}
				if(empty($aMatches[0][2])) {
					$sFunction .= $tt . '$xValue = $this->aFilters["' . $aMatches[0][1] . '"]->Get($xValue);' . $n;	
				} 
				else {
					$sFunction .= $tt . '$xValue = $this->aFilters["' . $aMatches[0][1] . '"]->Get($xValue, ' . $aMatches[0][2] . ');' . $n;	
				}
			}
		}
		$sFunction .= $tt . 'return $xValue;' . $n;
		$sFunction .= $t . '}' . $nn;
		
		$this->aVariables[$sName]['function'] = $sFunction;		
	}
	
	private function _stringToVariable($sString)
	{
		if(!preg_match('/[\'"]/', $sString) and !is_numeric($sString)) {
			$sString = '$this->aData["' . $sString . '"]';
		}

		return $sString;
	}
}

# End of file