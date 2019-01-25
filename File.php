<?php
/**
 * File class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage File
 * @filesource
 */

namespace Luki;

class File
{

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function getMimeType($file = '')
    {
        $mimeType = null;

        if (is_file($file)) {
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($file);
        }

        return $mimeType;
    }

    public static function getFilesInDirectory($directory)
    {
        $files = array();
        $dir   = dir($directory);

        while (($dirName = $dir->read()) !== false) {
            if ($dirName != '.' and $dirName != '..') {
                $files[] = $dirName;
            }
        }
        asort($files);

        return $files;
    }

    public static function getSafeDir($id, $level = 4)
    {
        $hash = hash('sha256', $id);
        $dir  = '';

        for ($i = 0; $i < $level; $i++) {
            $dir .= ord(substr($hash, $i, 1)).DIRECTORY_SEPARATOR;
        }

        return $dir;
    }

    public static function createDir($structure, $mode = 0755)
    {
        $isCreated = false;
        if (!is_dir($structure) and mkdir($structure, $mode, true)) {
            $isCreated = true;
        }

        return $isCreated;
    }
}