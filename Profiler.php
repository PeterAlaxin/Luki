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
class Profiler
{

    private $_profiler = array();
    private $_memory;
    private $_isAjax = FALSE;
    private $_debug = array(); 

    public function __construct($microTime, $memory)
    {
        $this->_isAjax = Storage::Request()->isAjax();

        if ( !$this->_isAjax ) {
            Time::stopwatchStart('Luki_Profiler_PageTimer', $microTime);
            $this->_memory = $memory;
        }

        unset($microTime, $memory);
    }

    function __destruct()
    {
        if ( !$this->_isAjax ) {
            $time = Time::getStopwatch('Luki_Profiler_PageTimer');
            $memory = memory_get_usage();

            $this->_startProfiler();
            $this->_insideCell('Page time', $this->changeSecToMs($time) . ' ms');
            $this->_showMemory($memory);
            $this->_showSession();
            $this->_showTemplate();
            $this->_showData();
            $this->_showCache();
            $this->_showDebug();
            $this->_endProfiler();
        }
        
        unset($time, $memory);
    }

    public function Add($key, $value)
    {
        $this->_profiler[$key][] = $value;
    }

    public function debug($value, $name=NULL)
    {
        $this->_debug[] = array(
          'fnc' => (is_object($value) or is_array($value) or is_bool($value)) ? 'dump' : 'echo', 
          'name' => $name, 
          'value' => $value
          );
    }

    private function _showMemory($memory)
    {
        $memory = $memory - $this->_memory;
        $unit = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
        $totalMemory = @round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2) . ' ' . $unit[$i];
        $this->_insideCell('Memory', $totalMemory);

        unset($memory, $unit, $totalMemory);
    }

    private function _showSession()
    {
        if ( !empty($this->_profiler['Session']) ) {
            $sessions = $this->_profiler['Session'];
            $hidden = '<table>';
            foreach ( $sessions as $key => $session ) {
                $hidden .= '<tr><td>';

                if ( 0 == $key ) {
                    $hidden .= 'Start:';
                } else {
                    $hidden .= 'Change:';
                }

                $hidden .= '</td><td>' . $session . '</td></tr>';
            }
            $hidden .= '</table>';
        }

        $this->_insideCell('Session', count($sessions) . 'x', $hidden);
        
        unset($sessions, $hidden, $key, $session);
    }

    private function _showTemplate()
    {
        $times = 0;

        if ( !empty($this->_profiler['Template']) ) {
            $templates = $this->_profiler['Template'];
            $hidden = '<table>';
            foreach ( $templates as $template ) {
                $time = $this->changeSecToMs($template['time']);
                $times += $time;
                $hidden .= '<tr><td>&nbsp;' . $template['name'] . ':&nbsp;</td>';
                $hidden .= '<td>&nbsp;' . $time . '&nbsp;ms&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        }

        if ( !empty($templates) ) {
            $this->_insideCell('Template', count($templates) . 'x (' . $times . ' ms)', $hidden);
        }
        
        unset($times, $templates, $hidden, $template, $time);
    }

    private function _showData()
    {
        $times = 0;

        if ( !empty($this->_profiler['Data']) ) {
            $datas = $this->_profiler['Data'];
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ( $datas as $data ) {
                $time = $this->changeSecToMs($data['time']);
                $times += $time;
                $hidden .= '<tr><td>&nbsp;' . $data['sql'] . '&nbsp;</td>';
                $hidden .= '<td>&nbsp;' . number_format($time, 2) . '&nbsp;ms&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        }

        $this->_insideCell('Data', count($datas) . 'x (' . $times . ' ms)', $hidden);
        
        unset($times, $datas, $data, $hidden, $time);
    }

    private function _showCache()
    {
        if ( !empty($this->_profiler['Cache']) ) {
            $datas = $this->_profiler['Cache'];
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ( $datas as $data ) {
                $hidden .= '<tr><td>&nbsp;' . $data['type'] . '&nbsp;</td><td>&nbsp;' . $data['key'] . '&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        }
        else {
            $datas = array();
            $hidden = '';
        }

        $this->_insideCell('Cache', count($datas) . 'x', $hidden);
        
        unset($datas, $data, $hidden);
    }

    private function _showDebug()
    {
        if ( !empty($this->_debug) ) {
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ( $this->_debug as $data ) {
                if($data['fnc'] == 'echo') {
                    $hidden .= '<tr><td>&nbsp;' . $data['name'] . '&nbsp;</td><td>&nbsp;' . $data['value'] . '&nbsp;</td></tr>';
                }
                else {
                    ob_start();
                    var_dump($data['value']);
                    $result = ob_get_clean();
                    $hidden .= '<tr><td>&nbsp;' . $data['name'] . '&nbsp;</td><td><pre>' . $result . '</pre></td></tr>';
                }
            }
            $hidden .= '</table>';
        }
        else {
            $hidden = '';
        }

        $this->_insideCell('Debug', count($this->_debug) . 'x', $hidden);
        
        unset($data, $hidden);
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
    }

    private function _insideCell($title, $content, $hidden = NULL)
    {
        echo '<div style="padding: 10px 5px; float: left; border-right: 1px solid #000; text-align: center;';

        if ( !empty($hidden) and TRUE ) {
            $random = str_shuffle('acdefghijklmnopqrstuvwxz');
            echo 'cursor:pointer;" onClick="var el = document.getElementById(\'' . $random . '\'); if(el.style.display == \'none\') { var elements = document.getElementsByClassName(\'subProfiler\'); for (var i = 0; i < elements.length; i++) { elements[i].style.display = \'none\'; } el.style.display = \'block\'; } else { el.style.display = \'none\'; }">';
            echo '<div class="subProfiler" id="' . $random . '" style="border: 1px solid green; position: fixed; bottom: 36px; left: 0; padding: 5px; width: 100%; display: none;text-align: left; background-color: #eee; max-height: 300px; overflow: auto;"><b>' . $title . '</b><br />' . $hidden . '</div>';
        } else {
            echo '">';
        }

        echo '<b>' . $title . ':</b>&nbsp;';
        echo $content;
        echo '&nbsp;</div>';
        
        unset($title, $content, $hidden, $random);
    }

    private function changeSecToMs($seconds)
    {
        $miliSeconds = round($seconds * 1000, 2);

        unset($seconds);
        return $miliSeconds;
    }

}

# End of file