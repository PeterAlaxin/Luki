<?php
/**
 * Date template filter adapter
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

class Date
{

    public function Get($value, $format = '%d.%m.%Y', $timeZoneName = '')
    {
        if (empty($value)) {
            return '';
        }

        if (empty($timeZoneName)) {
            $timeZoneName = date_default_timezone_get();
        }

        $timeZone = new \DateTimeZone($timeZoneName);

        if (is_a($value, 'DateTime')) {
            $date = $value;
            $date->setTimezone($timeZone);
        } else {
            $date = new \DateTime($value, $timeZone);
        }

        if ('human' == $format) {
            $formatedDate = $this->getHuman($date->getTimestamp());
        } elseif (strpos($format, '%') === false) {
            $formatedDate = $date->format($format);
        } else {
            $formatedDate = strftime($format, $date->getTimestamp());
        }

        return $formatedDate;
    }

    private function getHuman($timestamp)
    {
        if ($timestamp >= strtotime('+7 day 00:00')) {
            $text = strftime('%e. %B %G', $timestamp);
        } elseif ($timestamp >= strtotime('+2 day 00:00')) {
            $text = $this->translate('Next ') . strftime('%A, %k:%M', $timestamp);
        } elseif ($timestamp >= strtotime('tomorrow 00:00')) {
            $text = $this->translate('Tomorrow at ') . strftime('%k:%M', $timestamp);
        } elseif ($timestamp >= strtotime('today 00:00')) {
            $text = strftime('%k:%M', $timestamp);
        } elseif ($timestamp >= strtotime('yesterday 00:00')) {
            $text = $this->translate('Yesterday at ') . strftime('%k:%M', $timestamp);
        } elseif ($timestamp >= strtotime('-6 day 00:00')) {
            $text = strftime('%A, %k:%M', $timestamp);
        } else {
            $text = strftime('%e. %B %G', $timestamp);
        }

        return $text;
    }

    private function translate($text)
    {
        if (0 === strpos(setlocale(LC_ALL, 0), 'sk_SK')) {
            $tran = $this->translateToSlovak($text);
        } else {
            $tran = $text;
        }

        return $tran;
    }

    private function translateToSlovak($text)
    {
        switch ($text) {
            case 'Tomorrow at ':
                $tran = 'Zajtra o ';
                break;
            case 'Yesterday at ':
                $tran = 'Včera o ';
                break;
            case 'Next ':
                $tran = 'Budúci ';
                break;
            default :
                $tran = $text;
        }

        return $tran;
    }
}
