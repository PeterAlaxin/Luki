<?php

/**
 * File class
 *
 * Luki framework
 * Date 19.9.2012
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
 * File class
 *
 * Files management
 *
 * @package Luki
 */
class File
{

    public static function getMimeType($file = '')
    {
        $mimeType = NULL;

        if ( is_file($file) ) {
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($file);
        }

        unset($file, $fileInfo);
        return $mimeType;
    }

    public static function getFilesInDirectory($directory)
    {
        $files = array();
        $dir = dir($directory);

        while ( ($dirName = $dir->read()) !== false ) {
            if ( $dirName != '.' and $dirName != '..' ) {
                $files[] = $dirName;
            }
        }
        asort($files);

        return $files;
    }

    public function getSafeDir($id, $level = 4)
    {
        $hash = hash('sha256', $id);
        $dir = '';
        
        for($i = 0; $i < $level; $i++) {
            $dir .= ord(substr($hash, $i, 1)) . DIRECTORY_SEPARATOR;
        }
        
        unset($id, $hash);
        return $dir;
    }

    public function createDir($structure, $mode = 0755)
    {
        $isCreated = FALSE;
        if (mkdir($structure, $mode, true)) {
            $isCreated = TRUE;
        }

        unset($structure, $mode);
        return $isCreated;
    }
}

# End of file