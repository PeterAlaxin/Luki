<?php

/**
 * Controller class
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

namespace Luki;

use Luki\Loader;
use Luki\Template;

/**
 * Controller class
 *
 * MVC Controller
 *
 * @package Luki
 */
class Controller {

	protected $bRender = TRUE;
	protected $aModels = array();
	protected $sOutput = '';
	protected $aData = array();
	protected $aModelMethods = array();
	protected $sTemplateName = '';
	
	function __construct()
	{
		$aRouteToController = explode('\\', get_class($this));

		if(!empty($aRouteToController[0]) and !empty($aRouteToController[1])) {
			$this->sTemplateName = Loader::isFile($aRouteToController[0] . '/template/' . $aRouteToController[1] . '.twig');
		}

		unset($aRouteToController);
	}

	public function __call($sName = '', $aArguments = array())
	{
		$xReturn = NULL;

		foreach ($this->aModelMethods as $sModel => $aMethods) {
			if(in_array($sName, $aMethods)) {
				$xReturn = call_user_func_array(array($this->aModels[$sModel], $sName), $aArguments);
				break;
			}
		}

		unset($sName, $aArguments, $sModel, $aMethods);
		return $xReturn;
	}

	public function preDispatch()
	{
		return $this;
	}

	public function postDispatch()
	{
		if($this->bRender) {
			$this->Render();
		}
		
		return $this;
	}

	public function Set($sKey, $xValue = '')
	{
		$this->aData[$sKey] = $xValue;

		unset($sKey, $xValue);
		return $this;
	}

	public function Get($sKey)
	{
		$xReturn = NULL;

		if(isset($this->aData[$sKey])) {
			$xReturn = $this->aData[$sKey];
		}

		unset($sKey);
		return $xReturn;
	}

	public function addModel($sModel)
	{
		$sModelClassFileWithPath = Loader::isClass($sModel);

		if(!empty($sModelClassFileWithPath)) {
			$this->aModels[$sModel] = new $sModel;
			$this->aModelMethods[$sModel] = get_class_methods($this->aModels[$sModel]);
		}

		unset($sModel, $sModelClassFileWithPath);
		return $this;
	}

	public function removeModel($sModel)
	{
		if(isset($this->aModels[$sModel])) {
			unset($this->aModels[$sModel], $this->aModelMethods[$sModel]);
		}

		unset($sModel);
		return $this;
	}

	public function noRender()
	{
		$this->bRender = FALSE;
		
		return $this;
	}
	
	public function Render($sTemplateName = NULL, $aData = NULL)
	{
		if(empty($sTemplateName)) {
			$sTemplateName = $this->sTemplateName;
		}
		
		if(is_null($aData)) {
			$aData = $this->aData;
		}
		
		$oTemplate = new Template($sTemplateName, $aData);
		$this->sOutput = $oTemplate->Render();
	}
	
	public function getOutput()
	{
		return $this->sOutput;
	}

	public function Show($bExit = TRUE)
	{
		echo $this->Render()
				  ->getOutput();

		if($bExit) {
			exit;
		}

		unset($bExit);
		return $this;
	}

}

# End of file