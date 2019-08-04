<?php
/**
 * Date class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Date
 * @filesource
 */

namespace Luki;

use Luki\Time;

class Date
{
    public static $format        = 'Y-m-d';
    public static $dateValidator = '/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/';

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function setFormat($format = 'Y-m-d')
    {
        $oDate = date_create('now');
        if (false !== $oDate->format($format)) {
            self::$format = $format;
            $isSet        = true;
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
        self::$format = 'Y-m-d';
    }

    public static function validDate($date = 'now')
    {
        if (false !== date_create($date)) {
            $isValid = true;
        } else {
            $isValid = false;
        }

        return $isValid;
    }

    public static function addDay($date = null, $day = null)
    {
        if (is_null($day)) {
            if (is_null($date)) {
                $date = 'now';
                $day  = 1;
            } elseif (is_int($date)) {
                $day  = $date;
                $date = 'now';
            } elseif (is_string($date)) {
                $day = 1;
            }
        }

        $newDate     = false;
        $interval    = new \DateInterval('P0D');
        $interval->d = $interval->d + (int) $day;

        $dateObject = date_create($date);
        if (false !== $dateObject) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        return $newDate;
    }

    public static function addMinute($date = null, $minute = null)
    {
        if (is_null($minute)) {
            if (is_null($date)) {
                $date   = 'now';
                $minute = 1;
            } elseif (is_int($date)) {
                $minute = $date;
                $date   = 'now';
            } elseif (is_string($date)) {
                $minute = 1;
            }
        }

        $newDate     = false;
        $interval    = new \DateInterval('P0D');
        $interval->i = $interval->i + (int) $minute;

        $dateObject = date_create($date);
        if (false !== $dateObject) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        return $newDate;
    }

    public static function addMonth($date = null, $month = null)
    {
        if (is_null($month)) {
            if (is_null($date)) {
                $date  = 'now';
                $month = 1;
            } elseif (is_int($date)) {
                $month = $date;
                $date  = 'now';
            } elseif (is_string($date)) {
                $month = 1;
            }
        }

        $newDate     = false;
        $interval    = new \DateInterval('P0M');
        $interval->m = $interval->m + (int) $month;

        $dateObject = date_create($date);
        if (false !== $dateObject) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        return $newDate;
    }

    public static function addYear($date = null, $year = null)
    {
        if (is_null($year)) {
            if (is_null($date)) {
                $date = 'now';
                $year = 1;
            } elseif (is_int($date)) {
                $year = $date;
                $date = 'now';
            } elseif (is_string($date)) {
                $year = 1;
            }
        }

        $newDate     = false;
        $interval    = new \DateInterval('P0Y');
        $interval->y = $interval->y + $year;

        $dateObject = date_create($date);
        if (false !== $dateObject) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        return $newDate;
    }

    public static function createDate($year = null, $month = null, $day = null)
    {
        if (is_null($year)) {
            $year = date('Y');
        }
        if (is_null($month)) {
            $month = date('m');
        }
        if (is_null($day)) {
            $day = date('d');
        }

        $date = date(self::$format, mktime(0, 0, 0, $month, $day, $year));

        return $date;
    }

    public static function revertDate($date)
    {
        $newDate      = false;
        $oldDelimiter = null;

        if (self::validDate($date)) {
            if (strpos($date, '.') !== false) {
                $oldDelimiter = '.';
                $newDelimiter = '-';
            } elseif (strpos($date, '-') !== false) {
                $oldDelimiter = '-';
                $newDelimiter = '.';
            }

            if (!is_null($oldDelimiter)) {
                $newDate = implode($newDelimiter, array_reverse(explode($oldDelimiter, $date)));
            }
        }

        return $newDate;
    }

    public static function diffDate($format, $dateFrom, $dateTo, $usingTimestamps = false)
    {
        if ($usingTimestamps) {
            $dateFrom = date('Y-m-d H:i:s', $dateFrom);
            $dateTo   = date('Y-m-d H:i:s', $dateTo);
        }

        $interval = date_diff(date_create($dateFrom), date_create($dateTo));

        switch ($format) {
            case 'yyyy':  # Depricated
            case 'y':
                $dateDiff = $interval->y;
                break;
            case "q":
                $dateDiff = ($interval->y * 4) + ($interval->m / 3);
                break;
            case "m":
                $dateDiff = ($interval->y * 12) + $interval->m;
                break;
            case "d":
                $dateDiff = $interval->days;
                break;
            case "ww":  # Depricated
            case "w":
                $dateDiff = $interval->days / 7;
                break;
            case "h":
                $dateDiff = ($interval->days * 24) + $interval->h;
                break;
            case "n": # Depricated
            case "i":
                $dateDiff = ((($interval->days * 24) + $interval->h) * 60) + $interval->i;
                break;
            case "s":
                $dateDiff = ((($interval->days * 24) + $interval->h) * 3600) + ($interval->i * 60) + $interval->s;
                break;
            default:
                $dateDiff = $interval->format($format);
                break;
        }

        return $dateDiff;
    }

    public static function nextWorkingDay($date = null)
    {
        $nextWorkingDay = false;
        if (is_null($date)) {
            $date = date('Y-m-d');
        }

        if (self::validDate($date)) {
            $day            = strftime('%u', strtotime($date));
            $nextWorkingDay = $date;

            if ($day > 5) {
                $date           = self::addDay($date, 8 - $day);
                $nextWorkingDay = self::nextWorkingDay($date);
            }
        }

        return $nextWorkingDay;
    }

    public static function DateTimeToFormat($dateTime, $format = null)
    {
        $date      = false;
        $microTime = self::DateTimeToMicrotime($dateTime);

        if (is_null($format)) {
            $format = self::getFormat();
        }
        if (false !== $microTime) {
            $date = date($format, $microTime);
        }

        return $date;
    }

    public static function DateTimeToMicrotime($dateTime)
    {
        return strtotime($dateTime);
    }

    public static function getWeekNumber($date)
    {
        $date = new \DateTime($date);

        return $date->format("W");
    }

    public static function isEvenWeek($date)
    {
        $week = self::getWeekNumber($date);

        return ($week % 2 == 0);
    }

    public static function isOddWeek($date)
    {
        $week = self::getWeekNumber($date);

        return ($week % 2 != 0);
    }

    public static function getDayInWeekNumber($date)
    {
        $date = new \DateTime($date);

        return $date->format("w");
    }
}