<?php

/**
 * Basic config adapter
 *
 * Luki framework
 * Date 7.7.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Config;

use Luki\Config\basicInterface;

/**
 * Basic config adapter
 * 
 * @package Luki
 */
abstract class basicAdapter implements basicInterface
{

    const FILE_NOT_EXISTS = 'File "%s" does not exists!';
    const FILE_NOT_READABLE = 'File "%s" is not readable!';
    const FILE_NOT_WRITABLE = 'File "%s" is not writable!';
    const CONFIGURATION_NOT_SAVED = 'File "%s" not saved!';

    public $fileName = '';

    public $configuration = array();

    public function __construct($fileName, $allowCreate = FALSE)
    {
        try {
            if ( !is_file($fileName) ) {
                $this->createConfigFile($fileName, $allowCreate);
            }

            if ( !is_readable($fileName) ) {
                throw new \Exception(sprintf(self::FILE_NOT_READABLE, $fileName));
            }

            $this->fileName = $fileName;
        }
        catch ( \Exception $exception ) {
            exit($exception->getMessage());
        }

        unset($fileName, $allowCreate);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getSections()
    {
        $sections = array_keys($this->configuration);

        return $sections;
    }

    public function saveConfiguration()
    {
        if ( !empty($this->fileName) and is_file($this->fileName) and ! is_writable($this->fileName) ) {
            throw new \Exception(sprintf(self::FILE_NOT_WRITABLE, $this->fileName));
        }
    }

    public function getFilename()
    {
        return $this->fileName;
    }

    public function setConfiguration($configuration)
    {
        $isSaved = FALSE;

        if ( is_array($configuration) ) {
            $this->configuration = $configuration;
            $isSaved = TRUE;
        }

        unset($configuration);
        return $isSaved;
    }

    public function setFilename($fileName)
    {
        $isSaved = FALSE;

        if ( !empty($fileName) ) {
            $this->fileName = $fileName;
            $isSaved = TRUE;
        }

        unset($fileName);
        return $isSaved;
    }

    public function saveToFile($output)
    {
        $isSaved = FALSE;

        try {
            if ( file_put_contents($this->fileName, $output) === FALSE ) {
                throw new \Exception(sprintf(self::CONFIGURATION_NOT_SAVED, $this->fileName));
            }

            $isSaved = TRUE;
        }
        catch ( \Exception $oException ) {
            exit($oException->getMessage());
        }

        unset($output);
        return $isSaved;
    }

    public function createConfigFile($fileName, $allowCreate)
    {
        if ( !$allowCreate ) {
            throw new \Exception(sprintf(self::FILE_NOT_EXISTS, $fileName));
        }

        if ( $this->setFilename($fileName) ) {
            $this->saveConfiguration();
        }

        unset($fileName, $allowCreate);
    }

}

# End of file