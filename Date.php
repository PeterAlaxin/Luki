<?php

/**
 * Date class
 *
 * Luki framework
 * Date 30.11.2012
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

use Luki\Time;

/**
 * Date class
 *
 * Date manipulation
 *
 * @package Luki
 */
class Date
{

    public static $format = 'Y-m-d';
    public static $dateValidator = '/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/';

    public static function setFormat($format = 'Y-m-d')
    {
        $isSet = FALSE;

        $oDate = date_create('now');
        if ( FALSE !== $oDate->format($format) ) {
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
        self::$format = 'Y-m-d';
    }

    public static function validDate($date = 'now')
    {
        $isValid = FALSE;

        if ( FALSE !== date_create($date) ) {
            $isValid = TRUE;
        }

        unset($date);
        return $isValid;
    }

    public static function addDay($date = NULL, $day = NULL)
    {
        if ( is_null($day) ) {
            if ( is_null($date) ) {
                $date = 'now';
                $day = 1;
            } elseif ( is_int($date) ) {
                $day = $date;
                $date = 'now';
            } elseif ( is_string($date) ) {
                $day = 1;
            }
        }

        $newDate = FALSE;
        $interval = new \DateInterval('P0D');
        $interval->d = $interval->d + (int) $day;

        $dateObject = date_create($date);
        if ( FALSE !== $dateObject ) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        unset($date, $day, $interval, $dateObject);
        return $newDate;
    }

    public static function addMinute($date = NULL, $minute = NULL)
    {
        if ( is_null($minute) ) {
            if ( is_null($date) ) {
                $date = 'now';
                $minute = 1;
            } elseif ( is_int($date) ) {
                $minute = $date;
                $date = 'now';
            } elseif ( is_string($date) ) {
                $minute = 1;
            }
        }

        $newDate = FALSE;
        $interval = new \DateInterval('P0D');
        $interval->i = $interval->i + (int) $minute;

        $dateObject = date_create($date);
        if ( FALSE !== $dateObject ) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        unset($date, $minute, $interval, $dateObject);
        return $newDate;
    }

    public static function addMonth($date = NULL, $month = NULL)
    {
        if ( is_null($month) ) {
            if ( is_null($date) ) {
                $date = 'now';
                $month = 1;
            } elseif ( is_int($date) ) {
                $month = $date;
                $date = 'now';
            } elseif ( is_string($date) ) {
                $month = 1;
            }
        }

        $newDate = FALSE;
        $interval = new \DateInterval('P0M');
        $interval->m = $interval->m + (int) $month;

        $dateObject = date_create($date);
        if ( FALSE !== $dateObject ) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        unset($date, $month, $interval, $dateObject);
        return $newDate;
    }

    public static function addYear($date = NULL, $year = NULL)
    {
        if ( is_null($year) ) {
            if ( is_null($date) ) {
                $date = 'now';
                $year = 1;
            } elseif ( is_int($date) ) {
                $year = $date;
                $date = 'now';
            } elseif ( is_string($date) ) {
                $year = 1;
            }
        }

        $newDate = FALSE;
        $interval = new \DateInterval('P0Y');
        $interval->y = $interval->y + $year;

        $dateObject = date_create($date);
        if ( FALSE !== $dateObject ) {
            $dateObject->add($interval);
            $newDate = $dateObject->format(self::$format);
        }

        unset($date, $year, $interval, $dateObject);
        return $newDate;
    }

    public static function createDate($year = NULL, $month = NULL, $day = NULL)
    {
        if ( is_null($year) ) {
            $year = date('Y');
        }
        if ( is_null($month) ) {
            $month = date('m');
        }
        if ( is_null($day) ) {
            $day = date('d');
        }

        $date = date(self::$format, mktime(0, 0, 0, $month, $day, $year));

        unset($year, $month, $day);
        return $date;
    }

    public static function revertDate($date)
    {
        $newDate = FALSE;
        $oldDelimiter = NULL;

        if ( self::validDate($date) ) {
            if ( strpos($date, '.') !== FALSE ) {
                $oldDelimiter = '.';
                $newDelimiter = '-';
            } elseif ( strpos($date, '-') !== FALSE ) {
                $oldDelimiter = '-';
                $newDelimiter = '.';
            }

            if ( !is_null($oldDelimiter) ) {
                $newDate = implode($newDelimiter, array_reverse(explode($oldDelimiter, $date)));
            }
        }

        unset($date, $oldDelimiter, $newDelimiter);
        return $newDate;
    }

    /**
     * Compute difference between dates
     * 
     * Thank`s Dave (http://www.addedbytes.com/blog/code/php-datediff-function/)
     * 
     * $interval can be: 
     * yyyy - Number of full years 
     * q - Number of full quarters 
     * m - Number of full months 
     * y - Difference between day numbers (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".) 
     * d - Number of full days 
     * w - Number of full weekdays 
     * ww - Number of full weeks 
     * h - Number of full hours 
     * n - Number of full minutes 
     * s - Number of full seconds (default) 
     * 
     * @param string $interval
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $usingTimestamps
     * @return type
     */
    public static function diffDate($interval, $dateFrom, $dateTo, $usingTimestamps = FALSE)
    {
        if ( !$usingTimestamps ) {
            $dateFrom = strtotime($dateFrom, 0);
            $dateTo = strtotime($dateTo, 0);
        }

        $difference = $dateTo - $dateFrom; // Difference in seconds 
        switch ( $interval ) {
            case 'yyyy': // Number of full years 
                $yearsDifference = floor($difference / 31536000);
                if ( mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom), date("n", $dateFrom), date("j", $dateFrom), date("Y", $dateFrom) + $yearsDifference) > $dateTo ) {
                    $yearsDifference--;
                }
                if ( mktime(date("H", $dateTo), date("i", $dateTo), date("s", $dateTo), date("n", $dateTo), date("j", $dateTo), date("Y", $dateTo) - ($yearsDifference + 1)) > $dateFrom ) {
                    $yearsDifference++;
                }
                $dateDiff = $yearsDifference;
                unset($yearsDifference);
                break;
            case "q": // Number of full quarters 
                $quartersDifference = floor($difference / 8035200);
                while ( mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom), date("n", $dateFrom) + ($quartersDifference * 3), date("j", $dateTo), date("Y", $dateFrom)) < $dateTo ) {
                    $quartersDifference++;
                }
                $quartersDifference--;
                $dateDiff = $quartersDifference;
                unset($quartersDifference);
                break;
            case "m": // Number of full months 
                $monthsDifference = floor($difference / 2678400);
                while ( mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom), date("n", $dateFrom) + ($monthsDifference), date("j", $dateTo), date("Y", $dateFrom)) < $dateTo ) {
                    $monthsDifference++;
                }
                $monthsDifference--;
                $dateDiff = $monthsDifference;
                unset($monthsDifference);
                break;
            case 'y': // Difference between day numbers 
                $dateDiff = date("z", $dateTo) - date("z", $dateFrom);
                break;
            case "d": // Number of full days 
                $dateDiff = floor($difference / 86400);
                break;
            case "w": // Number of full weekdays 
                $daysDifference = floor($difference / 86400);
                $weeksDifference = floor($daysDifference / 7); // Complete weeks 
                $firstDay = date("w", $dateFrom);
                $daysRemainder = floor($daysDifference % 7);
                $oddDays = $firstDay + $daysRemainder; // Do we have a Saturday or Sunday in the remainder? 
                if ( $oddDays > 7 ) { // Sunday 
                    $daysRemainder--;
                }
                if ( $oddDays > 6 ) { // Saturday 
                    $daysRemainder--;
                }
                $dateDiff = ($weeksDifference * 5) + $daysRemainder;
                # Garbage
                unset($daysDifference, $weeksDifference, $firstDay, $daysRemainder, $oddDays);
                break;
            case "ww": // Number of full weeks 
                $dateDiff = floor($difference / 604800);
                break;
            case "h": // Number of full hours 
                $dateDiff = floor($difference / 3600);
                break;
            case "n": // Number of full minutes 
                $dateDiff = floor($difference / 60);
                break;
            default: // Number of full seconds (default) 
                $dateDiff = $difference;
                break;
        }

        unset($interval, $dateFrom, $dateTo, $usingTimestamps, $difference);
        return $dateDiff;
    }

    public static function nextWorkingDay($date = NULL)
    {
        $nextWorkingDay = FALSE;
        if ( is_null($date) ) {
            $date = date('Y-m-d');
        }

        if ( self::validDate($date) ) {
            $day = strftime('%u', strtotime($date));
            $nextWorkingDay = $date;

            if ( $day > 5 ) {
                $date = self::addDay($date, 8 - $day);
                $nextWorkingDay = self::nextWorkingDay($date);
            }
        }

        unset($day, $date);
        return $nextWorkingDay;
    }

    public static function DateTimeToFormat($dateTime, $format = NULL)
    {
        $oldFormat = self::getFormat();
        $microTime = self::DateTimeToMicrotime($dateTime);
        $date = FALSE;

        if ( is_null($format) ) {
            $format = $oldFormat;
        }

        if ( FALSE !== $microTime and self::setFormat($format) ) {
            $date = date(self::$format, $microTime);
        }

        self::setFormat($oldFormat);

        unset($dateTime, $microTime, $oldFormat, $format);
        return $date;
    }

    public static function DateTimeToMicrotime($dateTime)
    {
        $microTime = FALSE;
        $dateTime = explode(' ', $dateTime);

        if ( 1 === preg_match(self::$dateValidator, $dateTime[0]) ) {
            $date = explode('-', $dateTime[0]);

            if ( !isset($dateTime[1]) ) {
                $dateTime[1] = '00:00:00';
            }

            if ( 1 === preg_match(Time::$timeValidator, $dateTime[1]) ) {
                $time = explode(':', $dateTime[1]);
                $microTime = mktime($time[0], $time['1'], $time[2], $date[1], $date[2], $date[0]);
            }
        }

        unset($dateTime, $dateTime, $time, $date);
        return $microTime;
    }

}

# End of file