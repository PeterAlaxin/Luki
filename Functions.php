<?php

use Luki\Storage;

function default_exception_handler($e)
{
    echo '<h2>Something is wrong!</h2>';
    echo '<p>'.$e->getMessage().'</p>';
    echo '<pre>';
    echo '</pre>';
}

function fd($value, $name = null)
{
    if (Storage::isProfiler()) {
        Storage::Profiler()->debug($value, $name);
    }
}

function camelCase($str, array $noStrip = array())
{
    $str = preg_replace('/[^a-z0-9'.implode("", $noStrip).']+/i', ' ', $str);
    $str = trim($str);
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);

    return $str;
}

function _t($text, $section = '')
{
    $lng         = Storage::Get('lng');
    $translation = Storage::Language()->Get($text, $lng, $section);

    return $translation;
}
