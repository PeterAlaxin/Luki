<?php

/**
 * Crumb class
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

/**
 * Crumb class
 *
 * Prepare Crumb
 *
 * @package Luki
 */
class Luki_Dispatcher_Crumb {

    private $sUrl;
    
    private $aCrumb;
    
    private $nCrumbCount;
        
	/**
	 * Constructor
	 */
	public function __construct($sProject = '')
	{
        $this->sUrl = urldecode($_SERVER['REQUEST_URI']);

		# Remove project name
		if(!empty($sProject)) {
			 $this->sUrl = str_replace('/' . $sProject . '/', '',  $this->sUrl);
		}

        if('/' == substr($this->sUrl, 0, 1) and strlen($this->sUrl)>1) {
			$this->sUrl = substr($this->sUrl, 1);
		}

		if('/' == substr($this->sUrl, -1)) {
			$this->sUrl = substr($this->sUrl, 0, strlen($this->sUrl) - 1);
		}
        
        $this->aCrumb = explode('/',$this->sUrl);
        
        $this->nCrumbCount = count($this->aCrumb);
	}

    /**
	 * Get actual URL
	 * 
	 * @return string
	 */
	public function getURL()
	{
		return $this->sUrl;
	}

    /**
	 * Get crumb
	 *
	 * @param integer $nIndex Index on route
	 * @return string Crumb from route
	 */
	public function getCrumb($nIndex=NULL)
	{
		$sReturn = NULL;

		if(is_null($nIndex)) {
			$sReturn = $this->aCrumb;
		}
		else {
            $nIndex = (int)$nIndex;
			if(!empty($this->aCrumb[$nIndex])) {
				$sReturn = $this->aCrumb[$nIndex];
			}
		}

		unset($nIndex);

		return $sReturn;
	}
    
    /**
     * Return crumb count
     * @return integer
     */
    public function getCrumbCount()
    {
        return $this->nCrumbCount;
    }
    
}

# End of file