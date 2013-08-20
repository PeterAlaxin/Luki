<?php

/**
 * Dispatcher class
 *
 * Luki framework
 * Date 7.7.2013
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

use Luki\Config;
use Luki\Request;

/**
 * Dispatcher class
 *
 * @package Luki
 */
class Dispatcher {
    
    private $oCrumb;
    
    private $oConfig;
    
    private $bDispatched = FALSE;
    
    private $aCrumb = array();
    
    private $oControler;
    
	/**
	 * Constructor
	 */
	public function __construct(Request $oRequest, Config $oConfig)
	{
        $this->oCrumb = $oRequest;
        $this->oConfig = $oConfig;
        $this->aCrumb = $oRequest->getCrumb();
        
        unset($oConfig, $oRequest);
    }
    
    public function Dispatch()
    {
        $this->bDispatched = FALSE;
        $nCount = $this->oCrumb->getCrumbCount();
        $aRoutes = $this->oConfig->getSections();
        
        foreach($aRoutes as $sRoute) {
            $aRoute = $this->oConfig->getSection($sRoute);
            
            if($aRoute['count'] <= $nCount) {
                $this->_checkRoute($aRoute);
                
                if($this->bDispatched) {
                    $this->_prepareController($aRoute);
                    $sOutput = $this->oControler->getOutput();
                    return $sOutput;
                }
            }
        }
    }
    
    private function _checkRoute($aRoute) 
    {
        $aUrl = explode('/', $aRoute['url']);
        $bEqual = TRUE;
        
        for($i=0; $i<$aRoute['count']; $i++) {
            if($aUrl[$i] != $this->aCrumb[$i]) {
                $bEqual = FALSE;
                break;
            }
        }
        
        $this->bDispatched = $bEqual;
        
        unset($aRoute, $aUrl, $bEqual, $i);
    }
    
    private function _prepareController($aRoute)
    {
     	$sController = $aRoute['modul'] . '\\' . $aRoute['controller'];
		$this->oControler = new $sController;
        
        $aMethods = get_class_methods(get_class($this->oControler));
        
		if(in_array('preDispatch', $aMethods)) {
			$this->oControler->preDispatch();
		}

		$sAction = $aRoute['action'] . 'Action';
		if(in_array($sAction, $aMethods)) {
			$this->oControler->$sAction();
		}
		elseif(in_array('indexAction', $aMethods)) {
			$this->oControler->indexAction();
		}

		if(in_array('postDispatch', $aMethods)) {
			$this->oControler->postDispatch();
		}
        
        unset($aRoute, $sController, $aMethods, $sAction);
    }
}
