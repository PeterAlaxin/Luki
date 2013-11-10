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

	protected $RenderAllowed = TRUE;
	protected $Models = array();
	protected $Output = '';
	protected $Data = array();
	protected $ModelMethods = array();
	protected $TemplateName = '';
	protected $EndProgramAfterRender = TRUE;
    protected $RouteToController = array();
    
	function __construct()
	{
		$this->RouteToController = explode('\\', get_class($this));

        $this->setDefaultTemplate();
        $this->setDefaultModel();
	}

	public function __call($Name, $Arguments = array())
	{
		$Result = NULL;

		foreach ($this->ModelMethods as $sModel => $aMethods) {
			if(in_array($Name, $aMethods)) {
				$Result = call_user_func_array(array($this->Models[$sModel], $Name), $Arguments);
				break;
			}
		}

		unset($Name, $Arguments, $sModel, $aMethods);
		return $Result;
	}
    
    public function __set($Name, $Value)
    {
        $this->Data[$Name] = $Value;

        unset($Name, $Value);
    }
    
    public function __get($Name)
    {
        if(isset($this->Data[$Name])) {
            $Value = $this->Data[$Name];
        }
        else {
            $Value = NULL;
        }
        
        unset($Name);
        return $Value;
    }
    
    public function __isset($Name)
    {
        $isSet = isset($this->Data[$Name]);
        
        unset($Name);
        return $isSet;
    }
    
    public function __unset($Name)
    {
        unset($this->Data[$Name], $Name);
    }
    
	public function preDispatch()
	{
		return $this;
	}

	public function postDispatch()
	{
		if($this->RenderAllowed) {
			$this->Render();
		}
		
		return $this;
	}

    public function getTemplateName()
    {
        return $this->TemplateName;
    }
    
    public function changeTemplateName($NewTemplateName)
    {
        $this->TemplateName = $NewTemplateName;
        
        unset($NewTemplateName);
        return $this;
    }

	public function removeModel($Model)
	{
		if(isset($this->Models[$Model])) {
			unset($this->Models[$Model], $this->ModelMethods[$Model]);
		}

		unset($Model);
		return $this;
	}

	public function noRender()
	{
		$this->RenderAllowed = FALSE;
		
		return $this;
	}
	
	public function Render()
	{
		$oTemplate = new Template($this->TemplateName, $this->Data);
		$this->Output = $oTemplate->Render();
        
        unset($oTemplate);
        return $this;
	}
	
	public function getOutput()
	{
		return $this->Output;
	}

	public function Show()
	{
		echo $this->Render()
				  ->getOutput();

		if($this->EndProgramAfterRender) {
			exit;
		}

		return $this;
	}

    public function noEndProgramAfterRender()
    {
        $this->EndProgramAfterRender = FALSE;
        
        return $this;
    }
    
    private function setDefaultTemplate()
    {
        if(!empty($this->RouteToController[0]) and !empty($this->RouteToController[1])) {
			$this->TemplateName = Loader::isFile($this->RouteToController[0] . '/template/' . $this->RouteToController[1] . '.twig');
		}       
    }
    
    private function setDefaultModel()
    {
        if(!empty($this->RouteToController[0]) and 
           !empty($this->RouteToController[1])
          ) {
			$this->addModel($this->RouteToController[0] . '_model_' . $this->RouteToController[1]);
		}       
    }
    
	public function addModel($Model)
	{
		$ModelClassFileWithPath = Loader::isClass($Model);

		if(!empty($ModelClassFileWithPath)) {
            $ModelWithPath = '\\' . preg_replace('/_/', '\\', $Model) ;
			$this->Models[$Model] = new $ModelWithPath;
			$this->ModelMethods[$Model] = get_class_methods($this->Models[$Model]);
		}

		unset($Model, $ModelClassFileWithPath, $ModelWithPath);
		return $this;
	}
}

# End of file