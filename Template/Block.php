<?php

/**
 * Template block class
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
 * Template block class
 *
 * @package Luki
 */
class Luki_Template_Block {
	# Variables

	private $sTemplate = '';
	private $aBlocks = array();
	private $sName = '';
	private $nStartBlockLenght = 0;
	private $aVariables = array();
	private $aVariablesKeys = array();
	private $sSearchVariableString = '';
	private $sInternalConstants = array('l' => '{'
		, 'r' => '}'
		, '_NOW' => '');
	private $sParsedTemplate = '';

	# Block
	private $start_block = "{start:";
	private $end_block = "{end:";
	private $end_block_tag = "}";
	private $block_block = "{block:";

	# Variable
	private $start_variable = "{";
	private $end_variable = "}";
	private $separator = "|";

	# Memo
	private $start_memo = "/*";
	private $end_memo = "*/";

	# Formular
	private $start_form = "{form.";
	private $end_form = "}";

	# Include
	private $start_include = "{include:";
	private $end_include = "}";

	# Helpers
	private $aHelpers = array(
		'br' => "nl2br",
		'bytes' => "Template::Bytes",
		'capital' => "Luki_Template_Block::Capitalize",
		'capitall' => "Luki_Template_Block::CapitalizeAll",
		'column' => "Template::Column",
		'date' => "Regional::Date",
		'datetime' => "Regional::Datetime",
		'escape' => "htmlspecialchars",
		'html' => "Template::Html",
		'money' => "Regional::Money",
		'micro' => "Date::DateTimeToMicrotime",
		'news' => "Template::News",
		'nbsp' => "Template::Nbsp",
		'link' => "Template::Link",
		'lower' => "Template::Lower",
		'special' => "Template::SpecialCharacters",
		'truncate' => "Template::Truncate",
		'tran' => "Template::Translate",
		'upper' => "Template::Upper",
		'url' => "Template::URL",
		'wrap' => "Template::Wrap"
	);
	private $aHelpersKeys = array('br', 'bytes', 'capital', 'capitall', 'column'
		, 'date', 'datetime', 'escape', 'html', 'money'
		, 'micro', 'news', 'nbsp', 'link', 'lower', 'special'
		, 'truncate', 'tran', 'upper', 'url', 'wrap');

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
	function __construct($sTemplate)
	{
		$this->nStartBlockLenght = strlen($this->start_block);
		$this->sSearchVariableString = '/(' . $this->start_variable . ')([^ ].*?)(' . $this->end_variable . ')/';
		$this->sInternalConstants['_NOW'] = date('Y-m-d H:i:s');

		$this->sTemplate = $sTemplate;
		unset($sTemplate);

		$this->_saveBlockName();
		$this->_explodeBlocks();
		$this->_searchVariables();
		$this->_transformConstants();
	}

	public function Assign($sVariable, $xValue = NULL)
	{
		if(in_array($sVariable, $this->aVariablesKeys)) {
			$this->aVariables[$sVariable] = $xValue;
		}

		foreach ($this->aBlocks as $sBlockName => $oBlock) {
			$oBlock->Assign($sVariable, $xValue);
		}

		unset($sVariable, $xValue, $sBlockName);
		return $this;
	}

	public function Parse($sBlock = '')
	{
		# Parse this block
		if(empty($sBlock) or $sBlock == $this->sName) {
			$this->sParsedTemplate = $this->sTemplate;

			foreach ($this->aVariables as $sVariable => $xValue) {
				if(preg_match($this->_variable($sVariable), $this->sParsedTemplate)) {
					$this->sParsedTemplate = preg_replace($this->_variable($sVariable), $xValue, $this->sParsedTemplate);
				}

				if(preg_match($this->_variable($sVariable, 'formated'), $this->sParsedTemplate, $aMatches)) {
					foreach ($aMatches as $sMatch) {
						$this->sParsedTemplate = str_replace($sMatch, $this->_formatValue($sMatch, $xValue), $this->sParsedTemplate);
					}

					unset($aMatches, $sMatch);
				}
			}

			unset($sVariable, $xValue);
		}
		else {
			$aBlocks = explode('.', $sBlock);
			$sParent = array_shift($aBlocks);
			$sNext = implode('.', $aBlocks);
			$this->aBlocks[$sParent]->Parse($sNext);
			$this->sTemplate = preg_replace($this->_block($sParent), $this->aBlocks[$sParent]->Render() . $this->_block($sParent), $this->sTemplate);

			unset($aBlocks, $sParent, $sNext);
		}

		unset($sBlock);
		return $this;
	}

	public function Render()
	{
		$this->sParsedTemplate = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $this->sParsedTemplate);

		return $this->sParsedTemplate;
	}

	public function getName()
	{
		return $this->sName;
	}

	private function _saveBlockName()
	{
		$nStart = strpos($this->sTemplate, $this->start_block);
		$nEndStart = strpos($this->sTemplate, $this->end_block_tag, $nStart);

		$this->sName = substr($this->sTemplate, $nStart + $this->nStartBlockLenght, $nEndStart - $nStart - $this->nStartBlockLenght);

		$nEndEnd = strrpos($this->sTemplate, $this->end_block . $this->sName);

		$this->sTemplate = substr($this->sTemplate, $nEndStart + 1, $nEndEnd - $nEndStart - 1);

		unset($nStart, $nEndStart, $nEndEnd);
	}

	private function _explodeBlocks()
	{
		$nStart = strpos($this->sTemplate, $this->start_block);
		$nEndStart = $nStart;

		while (!($nStart === FALSE)) {
			while ($nEndStart <= $nStart) {
				$nEndStart = strpos($this->sTemplate, $this->end_block_tag, $nEndStart);
			}

			$sBlockName = substr($this->sTemplate, $nStart + $this->nStartBlockLenght, $nEndStart - $nStart - $this->nStartBlockLenght);
			$nEndBlock = strrpos($this->sTemplate, $this->end_block . $sBlockName . $this->end_block_tag);
			$sSeparedBlock = substr($this->sTemplate, $nStart, $nEndBlock - $nStart + strlen($this->end_block . $sBlockName . $this->end_block_tag));
			$this->aBlocks[$sBlockName] = new Luki_Template_Block($sSeparedBlock);
			$this->sTemplate = str_replace($sSeparedBlock, $this->block_block . $sBlockName . $this->end_block_tag, $this->sTemplate);

			$nStart = strpos($this->sTemplate, $this->start_block, $nStart);
			$nEndStart = $nStart;

			unset($sBlockName, $nEndBlock, $sSeparedBlock);
		}

		unset($nStart, $nEndStart);
	}

	private function _searchVariables()
	{
		preg_match_all($this->sSearchVariableString, $this->sTemplate, $aMatches, PREG_SET_ORDER);

		foreach ($aMatches as $aMatch) {
			$sVariable = $aMatch[2];

			if(strpos($sVariable, '.') > 0) {
				$sVariable = substr($sVariable, 0, strpos($sVariable, '.'));
			}
			else if(strpos($sVariable, $this->separator) > 0) {
				$sVariable = substr($sVariable, 0, strpos($sVariable, $this->separator));
			}

			$this->aVariables[$sVariable] = '';

			unset($aMatch, $sVariable);
		}

		$this->aVariablesKeys = array_keys($this->aVariables);

		unset($aMatches);
	}

	/**
	 * Transform defined constants
	 *
	 * @access private
	 * @uses Template::Assign() Assign constant
	 */
	private function _transformConstants()
	{
		foreach ($this->aVariables as $sVariable => $xContent) {

			if(defined($sVariable)) {
				$this->aVariables[$sVariable] = constant($sVariable);
			}
			elseif(!empty($this->sInternalConstants[$sVariable])) {
				$this->aVariables[$sVariable] = $this->sInternalConstants[$sVariable];
			}
#			elseif($this->oLanguage->isTranslation($sVariable)) {
#				$this->aVariables[$sVariable] = $this->oLanguage->getTranslation($sVariable);
#			}
		}

		unset($sVariable, $xContent);
	}

	/**
	 * Create correct variable format
	 *
	 * @param string $sVariable Variable name
	 * @return string
	 * @access private
	 */
	private function _variable($sVariable, $sMode = 'basic')
	{
		switch ($sMode) {
			case 'formated':
				$sReturn = '/' . $this->start_variable . $sVariable . '\\' . $this->separator . '[0-9a-z:\\' . '\\' . $this->separator . ']*' . $this->end_variable . '/';
				break;
			case 'basic':
			default:
				$sReturn = '/' . $this->start_variable . $sVariable . $this->end_variable . '/';
		}

		unset($sVariable, $sMode);
		return $sReturn;
	}

	private function _block($sBlockName)
	{
		$sReturn = '/' . $this->block_block . $sBlockName . $this->end_block_tag . '/';

		unset($sBlockName);
		return $sReturn;
	}

	/**
	 * Format variable
	 *
	 * @param string $sOneFormated Format variable
	 * @param mixed $xValue Value
	 * @return mixed
	 */
	private function _formatValue($sMatch, $xValue)
	{
		$sMatch = substr($sMatch, 1, strlen($sMatch) - 2);
		$aFormat = explode($this->separator, $sMatch);
		for ($i = 1; $i < count($aFormat); $i++) {

			if(in_array($aFormat[$i], $this->aHelpersKeys)) {
				$xValue = $this->$aFormat[$i]($xValue);
			}
			elseif(!(strpos($aFormat[$i], ':') === FALSE)) {
				$aSubFormat = explode(':', $aFormat[$i]);

				if(in_array($aSubFormat[0], $this->aHelpersKeys)) {
					$xValue = $this->$aSubFormat[0]($xValue, $aSubFormat[1]);
				}
			}
		}

		unset($sMatch, $aFormat, $i, $aSubFormat);
		return $xValue;
	}

	/**
	 * Call undefined function
	 *
	 * @param string $sName Function name
	 * @param array $aArguments Parameters
	 */
	public function __call($sName = '', $aArguments = array())
	{
		$xReturn = NULL;

		if(isset($this->aHelpers[$sName])) {

			if(strpos($this->aHelpers[$sName], '::') > 0) {
				$aClassFunction = explode('::', $this->aHelpers[$sName]);
				$xReturn = call_user_func_array($aClassFunction, $aArguments);
			}
			else {
				$xReturn = call_user_func_array($this->aHelpers[$sName], $aArguments);
			}
		}
		else {
			throw new Exception('Method does not exists: ' . $sName, E_ERROR);
		}

		unset($sName, $aArguments, $aClassFunction);
		return $xReturn;
	}

	/**
	 * Capitalize string helper
	 * @static
	 * @param string $sString String to capitalize
	 * @return string
	 * @uses Template::Lower() Lowercase string
	 */
	static public function Capitalize($sString)
	{
		$sString = self::Upper(mb_substr($sString, 0, 1, 'UTF-8')) . 
				   self::Lower(mb_substr($sString, 1, mb_strlen($sString, 'UTF-8') -1, 'UTF-8'));

		return $sString;
	}

	/**
	 * Capitalize all words helper
	 *
	 * @param string $sString String to capitalize
	 * @return string
	 * @uses Template::Lower() Lowercase string
	 */
	static public function CapitalizeAll($sString)
	{
		$sString = mb_convert_case($sString, MB_CASE_TITLE, 'UTF-8');

		return $sString;
	}

	/**
	 * Truncate string helper
	 *
	 * @param string $sString String to truncate
	 * @param integer $nLength New length
	 * @return string
	 */
	static public function Truncate($sString, $nLength)
	{
		$sNewString = substr($sString, 0, $nLength);

		if(strlen($sString) > $nLength) {
			$sNewString .= '...';
		}

		return $sNewString;
	}

	/**
	 * Wrap text helper
	 *
	 * @param string $sString Long text
	 * @param integer $nLength Row length
	 * @return string
	 */
	static public function Wrap($sString, $nLength)
	{
		$sNewString = wordwrap($sString, $nLength);
		$sNewString = nl2br($sNewString);

		return $sNewString;
	}

	/**
	 * Bytes convertor helper
	 *
	 * @param integer $nBytes Bytes number
	 * @return string
	 */
	static public function Bytes($nBytes)
	{
		if($nBytes < 1024) {
			$sBytes = number_format($nBytes, 0) . '&nbsp;B';
		}
		elseif($nBytes < 1048576) {
			$sBytes = number_format($nBytes / 1024, 2, ',', '.') . '&nbsp;kB';
		}
		elseif($nBytes < 1073741824) {
			$sBytes = number_format($nBytes / 1048576, 2, ',', '.') . '&nbsp;MB';
		}
		else {
			$sBytes = number_format($nBytes / 1073741824, 2, ',', '.') . '&nbsp;GB';
		}

		return $sBytes;
	}

	static public function URL($sString)
	{
		$sReturn = urlencode($sString);

		$sReturn = str_replace('+', '%20', $sReturn);
		$sReturn = str_replace('_', '%5F', $sReturn);
		$sReturn = str_replace('.', '%2E', $sReturn);
		$sReturn = str_replace('-', '%2D', $sReturn);

		return $sReturn;
	}

	/**
	 * Uppercase string
	 *
	 * @param string $sString String to uppercase
	 * @return string
	 */
	static public function Upper($sString)
	{
		$sString = mb_convert_case($sString, MB_CASE_UPPER, 'UTF-8');

		return $sString;
	}

	/**
	 * Lowercase string
	 *
	 * @param string $sString String to lowercase
	 * @return string
	 */
	static public function Lower($sString)
	{
		$sString = mb_convert_case($sString, MB_CASE_LOWER, 'UTF-8');

		return $sString;
	}

	/**
	 * Make link
	 *
	 * @param string $sString String to make link
	 * @return string
	 * @uses Storage::Get() Get Dispatcher from Storage
	 * @uses Url::makeLink() Make correct url
	 */
	static public function Link($sString)
	{
		$sString = Url::makeLink($sString, FALSE);

		return $sString;
	}

	/**
	 * Change spaces to &nbsp;
	 *
	 * @param string $sString String to lowercase
	 * @return string
	 */
	static public function Nbsp($sString)
	{
		$sString = str_replace(' ', '&nbsp;', $sString);

		return $sString;
	}

	/**
	 * Change htmlentities to HTML
	 *
	 * @param string $sString String to change
	 * @return string
	 */
	static public function Html($sString)
	{
		$sString = htmlspecialchars_decode($sString, ENT_QUOTES);

		return $sString;
	}

	/**
	 * Translation
	 *
	 * @param string $sString String to change
	 * @return string
	 */
	static public function Translate($sString)
	{
#		$sString = Storage::Get('Language')->getTranslation($sString);

		return $sString;
	}

	/**
	 * Fix prepositions
	 *
	 * @param string $sString String to change
	 * @return string
	 */
	static public function News($sString)
	{
#		$sString = Storage::Get('Language')->fixPrepositions($sString);

		return $sString;
	}

	/**
	 * Change HTML special characters
	 *
	 * @param string $sString String
	 * @return string
	 */
	static public function SpecialCharacters($sString)
	{
		$sString = htmlspecialchars_decode($sString, ENT_QUOTES);

		return $sString;
	}

	/**
	 * text to column helper
	 *
	 * @param string $sString Long text
	 * @param integer $nLength Row length
	 * @return string
	 */
	static public function Column($sString, $nCount = 2)
	{
		if((int) $nCount < 2 or empty($sString)) {
			return $sString;
		}

		$sNewString = '';
		$LastPosition = 0;
		$NewPosition = 0;

		$nLenght = strlen($sString);
		$nColLenght = (int) ($nLenght / $nCount);

		for ($i = 1; $i <= $nCount; $i++) {
			$sNewString .= '<span class="columns' . $nCount . ' columns' . $nCount . '_col' . $i . '">';

			if($LastPosition + $nColLenght > strlen($sString)) {
				$NewPosition = FALSE;
			}
			else {
				$NewPosition = strpos($sString, '.', $LastPosition + $nColLenght);
			}

			if($NewPosition === FALSE) {
				$sNewString .= substr($sString, $LastPosition);
			}
			else {
				$sNewString .= substr($sString, $LastPosition, $NewPosition - $LastPosition + 1);
			}
			$sNewString .= '</span>';
			$LastPosition = $NewPosition + 1;
		}

		return $sNewString;
	}

}

# End of file