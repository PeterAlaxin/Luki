<?php

/**
 * Time class
 *
 * Luki framework
 * Date 30.11.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Date;

/**
 * Time class
 *
 * Time manipulation
 *
 * @package Luki
 */
class Time
{

    public static $format = 'H:i:s';
    public static $timeValidator = '/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/';
    private static $_sections = array();

    public static function setFormat($format = 'H:i:s')
    {
        $isSet = FALSE;

        $date = date_create('now');
        if ( FALSE !== $date->format($format) ) {
            self::$format = $format;
            $isSet = TRUE;
        }

        unset($format);
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

        unset($usec, $sec);
        return $microTime;
    }

    public static function DateTimeToFormat($dateTime, $format = 'r')
    {
        $date = Date::DateTimeToFormat($dateTime, $format);

        unset($dateTime, $format);
        return $date;
    }

    public static function DateTimeToMicrotime($dateTime)
    {
        $microTime = Date::DateTimeToMicrotime($dateTime);

        unset($dateTime);
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

        if ( $offset < 0 ) {
            $interval->invert = 1;
        }

        $dateTimeUTC->add($interval);
        $dateTimeHere = $dateTimeUTC->format('Y-m-d H:i:s');

        unset($dateTime, $timeZone, $dateTimeZoneHere, $dateTimeZoneUTC, $dateTimeUTC, $offset, $interval);
        return $dateTimeHere;
    }

    public static function stopwatchStart($section = 'default', $microTime = NULL)
    {
        $start = FALSE;

        if ( !empty($section) ) {
            if ( empty($microTime) ) {
                $microTime = self::explodeMicrotime();
            }

            self::$_sections[$section] = array(
              'start' => $microTime,
              'stop' => 0,
              'result' => 0 );
            $start = self::$_sections[$section]['start'];
        }

        unset($section);
        return $start;
    }

    public static function getStopwatchStart($section = 'default')
    {
        $start = FALSE;

        if ( !empty(self::$_sections[$section]) ) {
            $start = self::$_sections[$section]['start'];
        }

        unset($section);
        return $start;
    }

    public static function stopwatchStop($section = 'default')
    {
        $stop = FALSE;

        if ( !empty(self::$_sections[$section]) ) {
            $stop = self::explodeMicrotime();
            self::$_sections[$section]['stop'] = $stop;
            self::$_sections[$section]['result'] = $stop - self::$_sections[$section]['start'];
        }

        unset($section);
        return $stop;
    }

    public static function getStopwatchStop($section = 'default')
    {
        $stop = FALSE;

        if ( !empty(self::$_sections[$section]) ) {
            $stop = self::$_sections[$section]['stop'];
        }

        unset($section);
        return $stop;
    }

    public static function getStopwatch($section = 'default')
    {
        $result = FALSE;

        if ( !empty(self::$_sections[$section]) ) {

            if ( empty(self::$_sections[$section]['result']) ) {
                self::stopwatchStop($section);
            }

            $result = self::$_sections[$section]['result'];
        }

        unset($section);
        return $result;
    }

}

# End of file