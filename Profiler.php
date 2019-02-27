<?php
/**
 * Profiler class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Profiler
 * @filesource
 */

namespace Luki;

class Profiler
{
    private $profiler = array();
    private $memory;
    private $isAjax   = false;
    private $debug    = array();
    private $error;

    public function __construct($microTime, $memory)
    {
        $this->isAjax = Storage::Request()->isAjax();

        if (!$this->isAjax) {
            Time::stopwatchStart('Luki_Profiler_PageTimer', $microTime);
            $this->memory = $memory;
        }
    }

    public function __destruct()
    {
        if (!$this->isAjax) {
            $time   = Time::getStopwatch('Luki_Profiler_PageTimer');
            $memory = memory_get_usage();

            $this->startProfiler();
            #$this->showInfo();
            $this->insideCell('Page time', $this->changeSecToMs($time).' ms');
            $this->showMemory($memory);
            $this->showRoute();
            $this->showSession();
            $this->showTemplate();
            $this->showData();
            $this->showCache();
            $this->showDebug();
            $this->showError();
            $this->endProfiler();
        }

        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Add($key, $value)
    {
        $this->profiler[$key][] = $value;
    }

    public function debug($value, $name = null)
    {
        $this->debug[] = array(
            'fnc'   => (is_object($value) or is_array($value) or is_bool($value)) ? 'dump' : 'echo',
            'name'  => $name,
            'value' => $value
        );
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    private function showRoute()
    {
        if (!empty($this->profiler['Route'])) {
            $this->insideCell('Route', $this->profiler['Route'][0]);
        }
    }

    private function showMemory($memory)
    {
        $memory      = $memory - $this->memory;
        $peak        = memory_get_peak_usage();
        $unit        = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $totalMemory = @round($memory / pow(1024, ($i           = floor(log($memory, 1024)))), 2).' '.$unit[$i];
        $peakMemory  = @round($peak / pow(1024, ($i           = floor(log($peak, 1024)))), 2).' '.$unit[$i];
        $this->insideCell('Memory', $totalMemory.' ('.$peakMemory.')');
    }

    private function showSession()
    {
        if (!empty($this->profiler['Session'])) {
            $sessions = $this->profiler['Session'];
            $hidden   = '<table>';
            foreach ($sessions as $key => $session) {
                $hidden .= '<tr><td>';

                if (0 == $key) {
                    $hidden .= 'Start:';
                } else {
                    $hidden .= 'Change:';
                }

                $hidden .= '</td><td>'.$session.'</td></tr>';
            }
            $hidden .= '</table>';
        }

        $this->insideCell('Session', count($sessions).'x', $hidden);
    }

    private function showTemplate()
    {
        $times = 0;

        if (!empty($this->profiler['Template'])) {
            $templates = $this->profiler['Template'];
            $hidden    = '<table>';
            foreach ($templates as $template) {
                $time   = $this->changeSecToMs($template['time']);
                $times  += $time;
                $hidden .= '<tr><td>&nbsp;'.$template['name'].':&nbsp;</td>';
                $hidden .= '<td>&nbsp;'.$time.'&nbsp;ms&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        }

        if (!empty($templates)) {
            $this->insideCell('Template', count($templates).'x ('.$times.' ms)', $hidden);
        }
    }

    private function showData()
    {
        $times = 0;

        if (!empty($this->profiler['Data'])) {
            $datas  = $this->profiler['Data'];
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ($datas as $data) {
                $time   = $this->changeSecToMs($data['time']);
                $times  += $time;
                $hidden .= '<tr><td>&nbsp;'.$data['sql'].'&nbsp;</td>';
                $hidden .= '<td>&nbsp;'.number_format($time, 2).'&nbsp;ms&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        }

        $this->insideCell('Data', count($datas).'x ('.$times.' ms)', $hidden);
    }

    private function showCache()
    {
        if (!empty($this->profiler['Cache'])) {
            $datas  = $this->profiler['Cache'];
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ($datas as $data) {
                $hidden .= '<tr><td>&nbsp;'.$data['type'].'&nbsp;</td><td>&nbsp;'.$data['key'].'&nbsp;</td></tr>';
            }
            $hidden .= '</table>';
        } else {
            $datas  = array();
            $hidden = '';
        }

        $this->insideCell('Cache', count($datas).'x', $hidden);
    }

    private function showDebug()
    {
        if (!empty($this->debug)) {
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            foreach ($this->debug as $data) {
                if ($data['fnc'] == 'echo') {
                    $hidden .= '<tr><td>&nbsp;'.$data['name'].'&nbsp;</td><td>&nbsp;'.$data['value'].'&nbsp;</td></tr>';
                } else {
                    ob_start();
                    var_dump($data['value']);
                    $result = ob_get_clean();
                    $hidden .= '<tr><td>&nbsp;'.$data['name'].'&nbsp;</td><td><pre>'.$result.'</pre></td></tr>';
                }
            }
            $hidden .= '</table>';
        } else {
            $hidden = '';
        }

        $this->insideCell('Debug', count($this->debug).'x', $hidden);
    }

    private function showError()
    {
        if (!empty($this->error)) {
            $hidden = '<table border="1" cellspacing="0" cellpadding="3">';
            $hidden .= '<tr><td>'.$this->error->getMessage().'</td></tr>';
            $hidden .= $this->readProgram($this->error->getFile(), $this->error->getLine());
            foreach ($this->error->getTrace() as $data) {
                $hidden .= $this->readProgram($data['file'], $data['line']);
            }
            $hidden   .= '</table>';
            $messagge = $this->error->getMessage();
        } else {
            $hidden   = '';
            $messagge = 'No error';
        }

        $this->insideCell('Error', $messagge, $hidden);
    }

    private function readProgram($file, $line)
    {
        $lineStart = max(0, $line - 3);
        $lineEnd   = $lineStart + 6;
        $source    = '<tr><td><b>'.$file.'</b><br /><br />';
        $program   = fopen($file, 'r');
        if ($program) {
            $lineNo      = 1;
            while (($programLine = fgets($program)) !== false) {
                if ($lineNo >= $lineStart and $lineNo <= $lineEnd) {
                    if ($lineNo == $line) {
                        $source .= $lineNo.'. <code><b>'.str_replace(' ', '&nbsp;', $programLine).'</b></code><br />';
                    } else {
                        $source .= $lineNo.'. <code>'.str_replace(' ', '&nbsp;', $programLine).'</code><br />';
                    }
                }
                $lineNo++;
            }
            fclose($program);
        }
        $source .= '</td></tr>';

        return $source;
    }

    private function showInfo()
    {
        $phpInfo = $this->getPhpInfo();
        $hidden  = '<table border="1" cellspacing="0" cellpadding="3" style="width: 1024px;"><tr><td style="padding: 10px;">';
        foreach ($phpInfo as $name => $section) {
            $hidden .= '<h2>'.$name.'</h2><table border="1" cellspacing="0" cellpadding="5" style="width: 1024px;">';
            foreach ($section as $key => $val) {
                if (is_array($val)) {
                    $hidden .= '<tr><td style="width: 33%; font-weight: bold;">'.$key.'</td><td style="width: 33%; word-wrap: break-word;">'.$val[0].'</td><td style="width: 33%; word-wrap: break-word;">'.$val[1].'</td></tr>';
                } elseif (is_string($key)) {
                    $hidden .= '<tr><td style="width: 33%; font-weight: bold;">'.$key.'</td><td colspan="2" style="word-wrap: break-word;">'.$val.'</td></tr>';
                } else {
                    $hidden .= '<tr><td colspan="3" style="word-wrap: break-word;">'.$val.'</td></tr>';
                }
            }
            $hidden .= '</table>';
        }

        $hidden .= '</td></tr></table>';

        $this->insideCell('PHP', str_replace(PHP_EXTRA_VERSION, '', phpversion()), $hidden);
    }

    private function getPhpInfo()
    {
        ob_start();
        phpinfo();
        $phpinfo = array(' ' => array());
        if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s',
                ob_get_clean(), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (strlen($match[1])) {
                    $phpinfo[$match[1]] = array();
                } elseif (isset($match[3])) {
                    $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
                } else {
                    $phpinfo[end(array_keys($phpinfo))][] = $match[2];
                }
            }
        }

        return $phpinfo;
    }

    private function startProfiler()
    {
        echo '<div style="width: 100%; height: 2px; outline: 1px solid #000; background-color: #ddd; color: #000; position: fixed; bottom: 0; left: 0; font-size: 13px; z-index: 10000;" id="LukiProfiler">';
        $this->insideCell('Luki', '3.2.0');
        $this->insideCell('PHP', (float) phpversion());
    }

    private function endProfiler()
    {
        echo '<span style="position: relative; top: 0; right: 5px; float: right; cursor: pointer;" onclick="var x=document.getElementById(\'LukiProfiler\'); x.style.display=\'none\';">X</span>';
        echo '</div>';
        echo '<style>#LukiProfiler:hover {min-height: 35px;}<style>';
    }

    private function insideCell($title, $content, $hidden = null)
    {
        echo '<div style="padding: 10px 5px; float: left; border-right: 1px solid #000; text-align: center;';

        if (!empty($hidden) and true) {
            $random = str_shuffle('acdefghijklmnopqrstuvwxz');
            echo 'cursor:pointer;" onClick="var el = document.getElementById(\''.$random.'\'); if(el.style.display == \'none\') { var elements = document.getElementsByClassName(\'subProfiler\'); for (var i = 0; i < elements.length; i++) { elements[i].style.display = \'none\'; } el.style.display = \'block\'; } else { el.style.display = \'none\'; }">';
            echo '<div class="subProfiler" id="'.$random.'" style="border: 1px solid green; position: fixed; bottom: 36px; left: 0; padding: 5px; width: 100%; display: none;text-align: left; background-color: #eee; max-height: 300px; overflow: auto;"><b>'.$title.'</b><br />'.$hidden.'</div>';
        } else {
            echo '">';
        }

        echo '<b>'.$title.':</b>&nbsp;';
        echo $content;
        echo '&nbsp;</div>';
    }

    private function changeSecToMs($seconds)
    {
        $miliSeconds = round($seconds * 1000, 2);

        return $miliSeconds;
    }
}