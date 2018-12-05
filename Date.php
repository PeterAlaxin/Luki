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

    public static function diffDate($interval, $dateFrom, $dateTo, $usingTimestamps = false)
    {
        if (!$usingTimestamps) {
            $dateFrom = strtotime($dateFrom, 0);
            $dateTo   = strtotime($dateTo, 0);
        }

        $difference = $dateTo - $dateFrom;
        switch ($interval) {
            case 'yyyy':
                $yearsDifference = floor($difference / 31536000);
                if (mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom), date("n", $dateFrom),
                        date("j", $dateFrom), date("Y", $dateFrom) + $yearsDifference) > $dateTo) {
                    $yearsDifference--;
                }
                if (mktime(date("H", $dateTo), date("i", $dateTo), date("s", $dateTo), date("n", $dateTo),
                        date("j", $dateTo), date("Y", $dateTo) - ($yearsDifference + 1)) > $dateFrom) {
                    $yearsDifference++;
                }
                $dateDiff           = $yearsDifference;
                unset($yearsDifference);
                break;
            case "q":
                $quartersDifference = floor($difference / 8035200);
                while (mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom),
                    date("n", $dateFrom) + ($quartersDifference * 3), date("j", $dateTo), date("Y", $dateFrom)) < $dateTo) {
                    $quartersDifference++;
                }
                $quartersDifference--;
                $dateDiff         = $quartersDifference;
                unset($quartersDifference);
                break;
            case "m":
                $monthsDifference = floor($difference / 2678400);
                while (mktime(date("H", $dateFrom), date("i", $dateFrom), date("s", $dateFrom),
                    date("n", $dateFrom) + ($monthsDifference), date("j", $dateTo), date("Y", $dateFrom)) < $dateTo) {
                    $monthsDifference++;
                }
                $monthsDifference--;
                $dateDiff        = $monthsDifference;
                unset($monthsDifference);
                break;
            case 'y':
                $dateDiff        = date("z", $dateTo) - date("z", $dateFrom);
                break;
            case "d":
                $dateDiff        = floor($difference / 86400);
                break;
            case "w":
                $daysDifference  = floor($difference / 86400);
                $weeksDifference = floor($daysDifference / 7);
                $firstDay        = date("w", $dateFrom);
                $daysRemainder   = floor($daysDifference % 7);
                $oddDays         = $firstDay + $daysRemainder;
                if ($oddDays > 7) {
                    $daysRemainder--;
                }
                if ($oddDays > 6) {
                    $daysRemainder--;
                }
                $dateDiff = ($weeksDifference * 5) + $daysRemainder;
                break;
            case "ww":
                $dateDiff = floor($difference / 604800);
                break;
            case "h":
                $dateDiff = floor($difference / 3600);
                break;
            case "n":
                $dateDiff = floor($difference / 60);
                break;
            default:
                $dateDiff = $difference;
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
        $oldFormat = self::getFormat();
        $microTime = self::DateTimeToMicrotime($dateTime);
        $date      = false;

        if (is_null($format)) {
            $format = $oldFormat;
        }

        if (false !== $microTime and self::setFormat($format)) {
            $date = date(self::$format, $microTime);
        }

        self::setFormat($oldFormat);

        return $date;
    }

    public static function DateTimeToMicrotime($dateTime)
    {
        $microTime = false;
        $dateTime  = explode(' ', $dateTime);

        if (1 === preg_match(self::$dateValidator, $dateTime[0])) {
            $date = explode('-', $dateTime[0]);

            if (!isset($dateTime[1])) {
                $dateTime[1] = '00:00:00';
            }

            if (1 === preg_match(Time::$timeValidator, $dateTime[1])) {
                $time      = explode(':', $dateTime[1]);
                $microTime = mktime($time[0], $time['1'], $time[2], $date[1], $date[2], $date[0]);
            }
        }

        return $microTime;
    }
}