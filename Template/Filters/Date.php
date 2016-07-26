<?php

/**
 * Date template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
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

namespace Luki\Template\Filters;

/**
 * Date template filter
 * 
 * @package Luki
 */
class Date
{

    public function Get($value, $format = '%d.%m.%Y', $timeZoneName = '')
    {
        if ( empty($timeZoneName) ) {
            $timeZoneName = date_default_timezone_get();
        }

        $timeZone = new \DateTimeZone($timeZoneName);

        if ( is_a($value, 'DateTime') ) {
            $date = $value;
            $date->setTimezone($timeZone);
        } else {
            $date = new \DateTime($value, $timeZone);
        }

        if ( 'human' == $format ) {
            $formatedDate = $this->getHuman($date->getTimestamp());
        } elseif ( strpos($format, '%') === FALSE ) {
            $formatedDate = $date->format($format);
        } else {
            $formatedDate = strftime($format, $date->getTimestamp());
        }

        unset($value, $format, $timeZoneName, $date, $timeZone);
        return $formatedDate;
    }

    private function getHuman($timestamp)
    {       
        if ($timestamp >= strtotime('+7 day 00:00')) {            
            $text = strftime('%e. %B %G', $timestamp);
fd($text, 'a');            
        } elseif ($timestamp >= strtotime('+2 day 00:00')) {
            $text = $this->translate('Next ') . strftime('%A, %k:%M', $timestamp);
fd($text, 'b');            
        } elseif ($timestamp >= strtotime('tomorrow 00:00')) {
            $text = $this->translate('Tomorrow at ') . strftime('%k:%M', $timestamp);
fd($text, 'c');            
        } elseif ($timestamp >= strtotime('today 00:00')) {
            $text = strftime('%k:%M', $timestamp);
fd($text, 'd');            
        } elseif ($timestamp >= strtotime('yesterday 00:00')) {
            $text = $this->translate('Yesterday at ') . strftime('%k:%M', $timestamp);
fd($text, 'e');            
        } elseif ($timestamp >= strtotime('-6 day 00:00')) {
            $text = strftime('%A, %k:%M', $timestamp);
fd($text, 'f');            
        } else {
            $text = strftime('%e. %B %G', $timestamp);
fd($text, 'g');            
        }

        return $text;
    }
    
    private function translate($text)
    {
        if('sk_SK' == locale_get_default()) {
            $tran = $this->translateToSlovak($text); 
        }
        else {
            $tran = $text;
        }
        
        unset($text);
        return $tran;
    }
    
    private function translateToSlovak($text)
    {
        switch($text) {
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
        
        unset($text);
        return $tran;
    }

}