<?php
/**
 * Ymdhm template filter adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template\Filters;

class Ymdhm
{

    public function Get($value, $mode = 'long')
    {
        $names = array(
            'long'  => array("year", "month", "day", "hour", "minute", "second"),
            'short' => array("yr", "mnth", "day", "hr", "min", "sec")
        );

        $seconds = floor($value);

        $minutes = intval($seconds / 60);
        $seconds -= ($minutes * 60);

        $hours   = intval($minutes / 60);
        $minutes -= ($hours * 60);

        $days  = intval($hours / 24);
        $hours -= ($days * 24);

        $months = intval($days / 31);
        $days   -= ($months * 31);

        $years  = intval($months / 12);
        $months -= ($years * 12);

        $result   = array();
        if ($years) $result[] = sprintf("%s %s%s", number_format($years), $names[$mode][0], $years == 1 ? "" : "s");
        if ($months) $result[] = sprintf("%s %s%s", number_format($months), $names[$mode][1], $months == 1 ? "" : "s");
        if ($days) $result[] = sprintf("%s %s%s", number_format($days), $names[$mode][2], $days == 1 ? "" : "s");
        if ($hours) $result[] = sprintf("%s %s%s", number_format($hours), $names[$mode][3], $hours == 1 ? "" : "s");
        if ($minutes && count($result) < 2)
                $result[] = sprintf("%s %s%s", number_format($minutes), $names[$mode][4], $minutes == 1 ? "" : "s");
        if (($seconds && count($result) < 2) || !count($result))
                $result[] = sprintf("%s %s%s", number_format($seconds), $names[$mode][5], $seconds == 1 ? "" : "s");

        return implode(", ", $result);
    }
}