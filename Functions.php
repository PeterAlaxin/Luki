<?php

use Luki\Storage;

function default_exception_handler($e)
{
    echo '<h2>Something is wrong!</h2>'."\n";

    if (Storage::isProfiler()) {
        Storage::Profiler()->setError($e);
    }

    if (Storage::isDevelopment()) {
        echo 'Error  : '.$e->getMessage()."<br>\n";
        echo 'In file: '.$e->getFile()."<br>\n";
        echo 'On line: '.$e->getLine()."<br>\n";

        foreach ($e->getTrace() as $item) {
            if (empty($item['file'])) {
                echo 'Function: '.$item['function']."<br>\n";
            } else {
                echo 'Called from: '.$item['file'].' Line: '.$item['line'].' Function: '.$item['function']."<br>\n";
            }
        }
        exit;
    }
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
    $lng         = Storage::Get('langPage');
    $translation = Storage::Language()->Get($text, $lng, $section);

    return $translation;
}

function _ta($text, $section = '', $fill = null)
{
    $lng         = Storage::Get('lng');
    $translation = Storage::Language()->Get($text, $lng, $section);

    if (is_array($fill)) {
        foreach ($fill as $from => $to) {
            $translation = str_replace($from, $to, $translation);
        }
    }

    if ($text == $translation) {
        $translation .= '['.$section.']';
    }

    return $translation;
}
