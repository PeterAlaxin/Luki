<?php
/**
 * Time class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Time
 * @filesource
 */

namespace Luki;

use Luki\Date;

class Time
{

    public static $format = 'H:i:s';
    public static $timeValidator = '/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/';
    private static $sections = array();

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function setFormat($format = 'H:i:s')
    {
        $date = date_create('now');
        if (false !== $date->format($format)) {
            self::$format = $format;
            $isSet = true;
        } else {
            $isSet = false;
        }

        return $isSet;
    }

    public static function getFormat()
    {
        return self::$format;
    }

    public static function resetFormat()
    {
        self::$format = 'H:i:s';
    }

    public static function explodeMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $microTime = ((float) $usec + (float) $sec);

        return $microTime;
    }

    public static function DateTimeToFormat($dateTime, $format = 'r')
    {
        $date = Date::DateTimeToFormat($dateTime, $format);

        return $date;
    }

    public static function DateTimeToMicrotime($dateTime)
    {
        $microTime = Date::DateTimeToMicrotime($dateTime);

        return $microTime;
    }

    public static function convertUtcToTimezone($dateTime)
    {
        $timeZone = date_default_timezone_get();

        $dateTimeZoneHere = new \DateTimeZone($timeZone);
        $dateTimeZoneUTC = new \DateTimeZone("UTC");

        $dateTimeUTC = new \DateTime($dateTime, $dateTimeZoneUTC);

        $offset = $dateTimeZoneHere->getOffset($dateTimeUTC);
        $interval = new \DateInterval('PT' . abs($offset) . 'S');

        if ($offset < 0) {
            $interval->invert = 1;
        }

        $dateTimeUTC->add($interval);
        $dateTimeHere = $dateTimeUTC->format('Y-m-d H:i:s');

        return $dateTimeHere;
    }

    public static function stopwatchStart($section = 'default', $microTime = null)
    {
        if (!empty($section)) {
            if (empty($microTime)) {
                $microTime = self::explodeMicrotime();
            }

            self::$sections[$section] = array(
                'start' => $microTime,
                'stop' => 0,
                'result' => 0);
            $start = self::$sections[$section]['start'];
        } else {
            $start = false;
        }

        return $start;
    }

    public static function getStopwatchStart($section = 'default')
    {
        if (!empty(self::$sections[$section])) {
            $start = self::$sections[$section]['start'];
        } else {
            $start = false;
        }

        return $start;
    }

    public static function stopwatchStop($section = 'default')
    {
        if (!empty(self::$sections[$section])) {
            $stop = self::explodeMicrotime();
            self::$sections[$section]['stop'] = $stop;
            self::$sections[$section]['result'] = $stop - self::$sections[$section]['start'];
        } else {
            $stop = false;
        }

        return $stop;
    }

    public static function getStopwatchStop($section = 'default')
    {
        if (!empty(self::$sections[$section])) {
            $stop = self::$sections[$section]['stop'];
        } else {
            $stop = false;
        }

        return $stop;
    }

    public static function getStopwatch($section = 'default')
    {
        if (!empty(self::$sections[$section])) {

            if (empty(self::$sections[$section]['result'])) {
                self::stopwatchStop($section);
            }

            $result = self::$sections[$section]['result'];
        } else {
            $result = false;
        }

        return $result;
    }
}
