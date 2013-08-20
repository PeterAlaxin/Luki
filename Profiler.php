<?php

/**
 * Profiler class
 *
 * Luki framework
 * Date 4.8.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Storage class
 *
 * Useful storage for any informations
 *
 * @package Luki
 */
class Profiler {

	/**
	 * Flag for installed storage
	 *
	 * @access private
	 */
	private $_profiler = array();
    
    private $nMemory;

    public function __construct($aMicrotime, $nMemory)
    {
        Time::stopwatchStart('Luki_Profiler_PageTimer', $aMicrotime);
        $this->nMemory = $nMemory;
        
        unset($nMemory);
    }
    
    function __destruct() 
    {
        Time::stopwatchStop('Luki_Profiler_PageTimer') ;
        $nMemory = memory_get_usage();
        
        $this->_startProfiler();
        $this->_insideCell('Page time', round(Time::getStopwatch('Luki_Profiler_PageTimer'), 4) . ' s');
        $this->_showMemory($nMemory);
        $this->_showSession();
        $this->_endProfiler();
    }
    
    public function Add($sKey, $sValue) 
    {
        $this->_profiler[$sKey][] = $sValue;
    }
    
    private function _showMemory($nMemory)
    {
        $nMemory = $nMemory - $this->nMemory;
        $aUnit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $nTotalMemory = @round($nMemory/pow(1024,($i=floor(log($nMemory,1024)))),2).' '.$aUnit[$i];
        $this->_insideCell('Memory', $nTotalMemory);
        
        unset($nMemory, $aUnit, $nTotalMemory);
    }
    
    private function _showSession()
    {
        if(!empty($this->_profiler['Session'])) {
            $aSession = $this->_profiler['Session'];
            $sHidden = '<table>';
            foreach($aSession as $nKey => $sSession) {
                $sHidden .= '<tr><td>';
                
                if(0 == $nKey) {
                    $sHidden .= 'Start:';
                }
                else {
                    $sHidden .= 'Change:';
                }
                
                $sHidden .= '</td><td>' . $sSession . '</td></tr>';
            }
            $sHidden .= '</table>';
        }
        
        $this->_insideCell('Session', count($aSession) . 'x', $sHidden);
    }
    
    private function _startProfiler()
    {
        echo '<div style="width: 100%; min-height: 20px; outline: 1px solid #000; background-color: #ddd; color: #000; position: fixed; bottom: 0; left: 0; font-size: 13px;">';
        $this->_insideCell('Luki', '3.0.0');
    }
    
    private function _endProfiler()
    {
        echo '</div>';        
    }
    
    private function _insideCell($sTitle, $sContent, $sHidden=NULL)
    {
        echo '<div style="padding: 10px 5px; float: left; border-right: 1px solid #000; text-align: center;';
        
        if(!empty($sHidden) and FALSE) {
            $sRandom = str_shuffle('acdefghijklmnopqrstuvwxz');
            echo 'cursor:pointer;" onClick="var el = document.getElementById(\'' . $sRandom . '\'); if(el.style.display == \'none\') { el.style.display = \'block\'; } else { el.style.display = \'none\'; }">';
            echo '<p class="subProfiler" id="' . $sRandom . '" style="border: 1px solid green; position: fixed; bottom: 23px; left: 0; padding: 5px; width: 100%; display: none;text-align: left; background-color: #eee;"><b>' . $sTitle . '</b><br />' . $sHidden . '</p>';
        }
        else {
            echo '">';
        }
        
        echo '<b>' . $sTitle . ':</b>&nbsp;';
        echo $sContent;
        echo '&nbsp;</div>';
    }
}

# End of file