<?php

/**
 * Template Block class
 *
 * Luki framework
 * Date 7.4.2013
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
 * Template Block class
 *
 * @package Luki
 */
class Luki_Template_Block {

	protected $aBlock;
	protected $sContent = '';
	protected $sTransformedContent = '';
	protected $aVariables = array();

	public function __construct($Block)
	{
		$this->aBlock = $Block;
		
		if(is_array($Block)) {
			$this->sContent = $Block[2];
		}
		else {
			$this->sContent = $Block;			
		}
		
		$this->_defineVariables();
		$this->_transformVariables();

		unset($Block);
	}

	public function getContent()
	{
		return $this->sTransformedContent;
	}

	public function getVariables()
	{
		return $this->aVariables;
	}

	private function _defineVariables()
	{
		preg_match_all('|{{ (.*) }}|U', $this->sContent, $aMatches, PREG_SET_ORDER);

		foreach ($aMatches as $aVariable) {
			$this->aVariables[] = new Luki_Template_Variable($aVariable[1]);
		}

		unset($aMatches, $aVariable);
	}
	
	private function _transformVariables()
	{
		$this->sTransformedContent = $this->sContent;
		foreach ($this->aVariables as $oVariable) {
			$this->sTransformedContent = str_replace('{{ ' . $oVariable->getContent() . ' }}', $oVariable->getCode(), $this->sTransformedContent);
		}

		unset($oVariable);
	}

}

# End of file