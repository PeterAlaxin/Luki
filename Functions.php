<?php

use Luki\Storage;

function fd($value, $name = NULL)
{
    if ( Storage::isProfiler() ) {
        Storage::Profiler()->debug($value, $name);
    }
    
    unset($value, $name);
}

function camelCase($str, array $noStrip = [])
{
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
#        $str = lcfirst($str);

        return $str;
}

function _t($text, $section='')        
{
    $lng = Storage::Get('lng');
    $translation = Storage::Language()->Get($text, $lng, $section);
    
    unset($text, $section, $lng);
    return $translation;
}