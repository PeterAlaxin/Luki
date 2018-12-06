<?php
/**
 * Basic config adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Config
 * @filesource
 */

namespace Luki\Config;

use Luki\Config\BasicInterface;
use Luki\Exception\ConfigException;

abstract class BasicAdapter implements BasicInterface
{
    const FILE_NOT_EXISTS         = 'File "%s" does not exists!';
    const FILE_NOT_READABLE       = 'File "%s" is not readable!';
    const FILE_NOT_WRITABLE       = 'File "%s" is not writable!';
    const CONFIGURATION_NOT_SAVED = 'File "%s" not saved!';

    public $fileName      = '';
    public $configuration = array();

    public function __construct($fileName, $allowCreate = false)
    {
        try {
            if (!is_file($fileName)) {
                $this->createConfigFile($fileName, $allowCreate);
            }

            if (!is_readable($fileName)) {
                throw new ConfigException(sprintf(self::FILE_NOT_READABLE, $fileName));
            }

            $this->fileName = $fileName;
        } catch (\Exception $exception) {
            throw new ConfigException($exception->getMessage());
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
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
        if (!empty($this->fileName) and is_file($this->fileName) and ! is_writable($this->fileName)) {
            throw new ConfigException(sprintf(self::FILE_NOT_WRITABLE, $this->fileName));
        }
    }

    public function getFilename()
    {
        return $this->fileName;
    }

    public function setConfiguration($configuration)
    {
        if (is_array($configuration)) {
            $this->configuration = $configuration;
            $isSaved             = true;
        } else {
            $isSaved = false;
        }

        return $isSaved;
    }

    public function setFilename($fileName)
    {
        if (!empty($fileName)) {
            $this->fileName = $fileName;
            $isSaved        = true;
        } else {
            $isSaved = false;
        }

        return $isSaved;
    }

    public function saveToFile($output)
    {
        try {
            if (false === file_put_contents($this->fileName, $output)) {
                throw new ConfigException(sprintf(self::CONFIGURATION_NOT_SAVED, $this->fileName));
            }

            $isSaved = true;
        } catch (\Exception $oException) {
            throw new ConfigException($oException->getMessage());
        }

        return $isSaved;
    }

    public function createConfigFile($fileName, $allowCreate)
    {
        if (!$allowCreate) {
            throw new ConfigException(sprintf(self::FILE_NOT_EXISTS, $fileName));
        }

        if ($this->setFilename($fileName)) {
            $this->saveConfiguration();
        }
    }
}