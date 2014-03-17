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
    
    private $bAjax = FALSE;

    public function __construct($aMicrotime, $nMemory)
    {
        $this->bAjax =Storage::Request()->isAjax();
        
        if(!$this->bAjax) {
            Time::stopwatchStart('Luki_Profiler_PageTimer', $aMicrotime);
            $this->nMemory = $nMemory;
        }
        
        unset($nMemory);
    }
    
    function __destruct() 
    {
        if(!$this->bAjax) {
            Time::stopwatchStop('Luki_Profiler_PageTimer');
            $nMemory = memory_get_usage();

            $this->_startProfiler();
            $this->_insideCell('Page time', $this->changeSecToMs(Time::getStopwatch('Luki_Profiler_PageTimer')) . ' ms');
            $this->_showMemory($nMemory);
            $this->_showSession();
            $this->_showTemplate();
            $this->_showData();
            $this->_endProfiler();
        }
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
    
    private function _showTemplate()
    {
        $nTimes = 0;
        
        if(!empty($this->_profiler['Template'])) {
            $aTemplates = $this->_profiler['Template'];
            $sHidden = '<table>';
            foreach($aTemplates as $aTemplate) {
                $nTime = $this->changeSecToMs($aTemplate['time']);
                $nTimes += $nTime;
                $sHidden .= '<tr><td>' . $aTemplate['name'] . ':</td>';
                $sHidden .= '<td>' . $nTime . '&nbsp;ms</td></tr>';
            }
            $sHidden .= '</table>';
        }
        
        if(!empty($aTemplates)) {
            $this->_insideCell('Template', count($aTemplates) . 'x (' . $nTimes . ' ms)', $sHidden);
        }
    }
    
    private function _showData()
    {
        $nTimes = 0;
        
        if(!empty($this->_profiler['Data'])) {
            $aDatas = $this->_profiler['Data'];
            $sHidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach($aDatas as $aData) {
                $nTime = $this->changeSecToMs($aData['time']);
                $nTimes += $nTime;
                $sHidden .= '<tr><td>' . $aData['sql'] . '</td>';
                $sHidden .= '<td>' . number_format($nTime, 2) . '&nbsp;ms</td></tr>';
            }
            $sHidden .= '</table>';
        }
        
        $this->_insideCell('Data', count($aDatas) . 'x (' . $nTimes . ' ms)', $sHidden);
    }
    
    private function _startProfiler()
    {
        echo '<div style="width: 100%; min-height: 20px; outline: 1px solid #000; background-color: #ddd; color: #000; position: fixed; bottom: 0; left: 0; font-size: 13px;" id="LukiProfiler">';
        $this->_insideCell('Luki', '3.0.0');
    }
    
    private function _endProfiler()
    {
        echo '<span style="position: relative; top: 0; right: 5px; float: right; cursor: pointer;" onclick="var x=document.getElementById(\'LukiProfiler\'); x.style.display=\'none\';">X</span>';
        echo '</div>';        
#        echo '</body></html>';        
    }
    
    private function _insideCell($sTitle, $sContent, $sHidden=NULL)
    {
        echo '<div style="padding: 10px 5px; float: left; border-right: 1px solid #000; text-align: center;';
        
        if(!empty($sHidden) and TRUE) {
            $sRandom = str_shuffle('acdefghijklmnopqrstuvwxz');
            echo 'cursor:pointer;" onClick="var el = document.getElementById(\'' . $sRandom . '\'); if(el.style.display == \'none\') { var elements = document.getElementsByClassName(\'subProfiler\'); for (var i = 0; i < elements.length; i++) { elements[i].style.display = \'none\'; } el.style.display = \'block\'; } else { el.style.display = \'none\'; }">';
            echo '<div class="subProfiler" id="' . $sRandom . '" style="border: 1px solid green; position: fixed; bottom: 36px; left: 0; padding: 5px; width: 100%; display: none;text-align: left; background-color: #eee; max-height: 300px; overflow: auto;"><b>' . $sTitle . '</b><br />' . $sHidden . '</div>';
        }
        else {
            echo '">';
        }
        
        echo '<b>' . $sTitle . ':</b>&nbsp;';
        echo $sContent;
        echo '&nbsp;</div>';
    }
    
    private function changeSecToMs($Sec)
    {
        $Ms = round($Sec*1000, 2);
        
        unset($Sec);
        return $Ms;
    }
}

# End of file