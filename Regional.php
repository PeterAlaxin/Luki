<?php

/**
 * Regional class
 *
 * Luki framework
 * Date 8.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * Regional class
 *
 * @package Luki
 */
class Regional
{

    public static $format = '%x';

    public static function setFormat($format = '%x')
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
        self::$format = '%x';
        
        return $this;
    }

    public static function Date($value = NULL, $format = NULL)
    {
        if ( empty($value) or
                '0000-00-00' == $value or ( $microValue = strtotime($value)) === FALSE ) {
            unset($value, $format, $microValue);
            return '';
        }

        $oldLocale = NULL;

        switch ( $format ) {
            case 'text':
                $format = '%e. %B %Y, %A';
                break;
            case 'gmt':
                $format = '%a, %d %b %Y %H:%M:%S GMT';
                $oldLocale = setlocale(LC_TIME, 0);
                setlocale(LC_TIME, 'en_US.utf8');
                break;
            case NULL;
            default:
                $format = self::$format;
        }

        $value = strftime($format, $microValue);

        if ( !is_null($oldLocale) ) {
            setlocale(LC_TIME, $oldLocale);
        }

        unset($format);
        return $value;
    }

    public static function Money($money, $format = NULL)
    {
        $userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
        if ( !empty($userAgent) and
                0 === preg_match('/windows/i', $userAgent) ) {

            switch ( $format ) {
                case 'eur':
                    $format = '%!n&nbsp;€';
                    break;
                default:
                    $format = '%!n';
            }

            $money = money_format($format, (float) $money);
        } else {
            $money = number_format((float) $money, 2, ',', '.');

            if ( 'eur' == $format ) {
                $money = $money . '&nbsp;€';
            }
        }

        unset($userAgent, $format);
        return $money;
    }

    public static function getDays($isShort = FALSE)
    {
        $days = array();
        $format = (bool) $isShort ? '%a' : '%A';

        for ( $day = 1; $day < 8; $day++ ) {
            $microValue = mktime(0, 0, 0, 1, $day, 2012);
            $days[] = strftime($format, $microValue);
        }

        unset($isShort, $day, $microValue, $format);
        return $days;
    }

    public static function getMonths($isShort = FALSE)
    {
        $months = array();
        $format = (bool) $isShort ? '%b' : '%B';

        for ( $month = 1; $month < 13; $month++ ) {
            $microValue = mktime(0, 0, 0, $month, 1, 2012);
            $months[] = strftime($format, $microValue);
        }

        unset($isShort, $month, $microValue, $format);
        return $months;
    }

    public static function setLocale($language)
    {
        setlocale(LC_ALL, $language);
        setlocale(LC_NUMERIC, 'C');

        unset($language);
        return $this;
    }

}

# End of file