<?php
/**
 * View class
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
 * View class
 *
 * @package Luki
 */
abstract class Luki_View
{
	private $oTemplate=NULL;
	
	/**
	 * Constructor
	 *
	 * @param array $aData Data from controller
	 * @uses MODULES_DIR Path to modules
	 * @uses Template Template class
	 */
	function __construct($sViewClassFileWithPath)
	{
		$sTemplate = preg_replace('/\/view\//', '/view/template/', $sViewClassFileWithPath);
		$sTemplate = preg_replace('/.php/', '.tpl', $sTemplate);
		
		$this->oTemplate = new Luki_Template($sTemplate);

		unset($sViewClassFileWithPath, $sTemplate);
	}

	/**
	 * Fill values to template
	 *
	 * @uses View::Set() Set saved variables
	 */
	public function fillValues($aData=array())
	{
		$this->aData = $aData;

		foreach($this->aData as $sVariable => $xValue) {
			if(is_object($xValue) and 'Formular' == get_class($xValue)) {
				$this->oTemplate->makeFormular($sVariable, $xValue);
			}
			else {
				$this->Set($sVariable, $xValue);
			}
		}

		unset($aData, $sVariable, $xValue);
		return $this;
	}

	/**
	 * Render view from template
	 *
	 * @uses Template::Render() Render main block
	 */
	public function Render()
	{
		$sReturn = $this->oTemplate->Render('main');

		return $sReturn;
	}

	/**
	 * Get variable from saved data
	 *
	 * @param string $sVariable Variable name
	 * @return mixed
	 */
	public function Get($sVariable)
	{
		$xReturn = NULL;

		if(isset($this->aData[$sVariable])) {
			$xReturn = $this->aData[$sVariable];
		}

		unset($sVariable);
		return $xReturn;
	}

	/**
	 * Set Variable
	 *
	 * @param string $sVariable Variable name
	 * @param mixed $xValue Variable value
	 * @uses Template::Assign() Assign variable value
	 */
	public function Set($sVariable, $xValue = '')
	{
		$this->oTemplate->Assign($sVariable, $xValue);

		unset($sVariable, $xValue);
		return $this;
	}

	/**
	 * End block
	 *
	 * @param string $sBlock Block name
	 * @uses Template::Parse() Render block
	 */
	public function End($sBlock)
	{
		$this->oTemplate->Parse($sBlock);

		unset($sBlock);
		return $this;
	}

}

# End of file