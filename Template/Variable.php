<?php

/**
 * Template Variable class
 *
 * Luki framework
 * Date 6.4.2013
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
 * Template Variable class
 *
 * @package Luki
 */
class Luki_Template_Variable {

	protected $sContent = '';
	protected $sVariable = '';
	protected $sFunctionName = '';
	protected $sFunction = '';
	protected $sTransformedVariable = '';
	protected $sCode = '';
	protected $aFilters = array();

	public function __construct($sContent)
	{
		$this->sContent = $sContent;

		$this->_prepareFilters();
		$this->_transformVariable();
		$this->_prepareCode();

		unset($sContent);
	}

	public function getCode()
	{
		return $this->sCode;
	}

	public function getContent()
	{
		return $this->sContent;
	}

	public function getFunctionName()
	{
		return $this->sFunctionName;
	}

	public function getFunction()
	{
		$this->_prepareFunction();

		return $this->sFunction;
	}

	private function _prepareFilters()
	{
		if(strpos($this->sContent, '|')) {
			$this->aFilters = explode('|', $this->sContent);
			$this->sVariable = array_shift($this->aFilters);

			$this->_prepareFunctionName();
		}
		else {
			$this->sVariable = $this->sContent;
		}
	}

	private function _prepareFunctionName()
	{
		$sFilters = implode('|', $this->aFilters);
		$this->sFunctionName = 'fnc_' . sha1($sFilters);

		unset($sFilters);
	}

	private function _transformVariable()
	{
		# Array
		preg_match('/^[\[{](.*)[\]}]$/', $this->sVariable, $aMatches);
		if(count($aMatches) > 0) {
			$aNewItems = array();
			$aItems = explode(', ', $aMatches[1]);

			foreach ($aItems as $Item) {
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

			$sVariable = preg_replace('/[\[{]/', 'array(', $this->sVariable);
			$sVariable = preg_replace('/' . $aMatches[1] . '/', implode(', ', $aNewItems), $sVariable);
			$this->sTransformedVariable = preg_replace('/[\]}]/', ')', $sVariable);
		}
		else {
			$this->sTransformedVariable = $this->_stringToVariable($this->sVariable);
		}

		unset($aMatches, $aNewItems, $aItems, $Item, $aSubItems, $sVariable);
	}

	private function _stringToVariable($sString)
	{
		if(!preg_match('/[\'"]/', $sString) and !is_numeric($sString)) {
			$sString = '$this->aData["' . $sString . '"]';
		}

		return $sString;
	}

	private function _prepareCode()
	{
		$sCode = '<?php echo ';
		if(!empty($this->aFilters)) {
			$sCode .= '$this->_' . $this->sFunctionName . '(' . $this->sTransformedVariable . ');';
		}
		else {
			$sCode .= $this->sTransformedVariable . ';';
		}
		$sCode .= ' ?>';

		$this->sCode = $sCode;

		unset($sCode);
	}

	private function _prepareFunction()
	{
		$t = chr(9);
		$tt = chr(9) . chr(9);
		$n = chr(10);
		$nn = chr(10) . chr(10);

		$sFunction = $t . 'private function _' . $this->sFunctionName . '($xValue)' . $n;
		$sFunction .= $t . '{' . $n;
		foreach ($this->aFilters as $sFilter) {

			preg_match_all('|(.*)\((.*)\)|U', $sFilter, $aMatches, PREG_SET_ORDER);

			if(empty($aMatches)) {
#				if(!in_array($sFilter, $this->aFilters)) {
#					continue;
#				}
				$sFunction .= $tt . '$xValue = $this->aFilters["' . $sFilter . '"]->Get($xValue);' . $n;
			}
			else {
#				if(!in_array($aMatches[0][1], $this->aFilters)) {
#					continue;
#				}
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

		$this->sFunction = $sFunction;

		unset($sFunction, $sFilter, $aMatches);
	}

}

# End of file