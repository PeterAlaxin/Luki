<?php
/**
 * Config class
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

namespace Luki;

use Luki\Config\BasicInterface;

class Config
{

    private $configuration = array();
    private $adapter = null;
    private $defaultSection = '';
    private $sections = array();

    public function __construct(BasicInterface $adapter)
    {
        $this->adapter = $adapter;
        $this->configuration = $this->adapter->getConfiguration();
        $this->sections = $this->adapter->getSections();

        if (isset($this->sections[0])) {
            $this->defaultSection = $this->sections[0];
        }
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function findAdapter($file)
    {
        $filePathInformation = pathinfo($file);
        $adapter = __NAMESPACE__ . '\Config\\' . ucfirst($filePathInformation['extension']) . 'Adapter';

        return $adapter;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getConfigurationFile()
    {
        return $this->adapter->getFilename();
    }

    public function addSection($sectionName, $values = array())
    {
        if (!empty($sectionName) and is_string($sectionName) and ! in_array($sectionName, $this->sections)) {
            $this->configuration[$sectionName] = array();
            $this->sections[] = $sectionName;
            $this->setDefaultSection($sectionName);
            $isAdded = true;

            if (!empty($values) and is_array($values)) {
                $this->addValue($values);
            }
        } else {
            $isAdded = false;
        }

        return $isAdded;
    }

    public function deleteSection($sectionName)
    {
        $section = $this->fillEmptySection($sectionName);

        if (in_array($section, $this->sections)) {
            unset($this->configuration[$section]);
            $this->sections = array_keys($this->configuration);
            $isDeleted = true;
        } else {
            $isDeleted = false;
        }

        return $isDeleted;
    }

    public function getSection($sectionName)
    {
        $section = $this->fillEmptySection($sectionName);
        $values = array();

        if (in_array($section, $this->sections)) {
            $values = $this->configuration[$section];
        }

        return $values;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function addValue($key, $value = '', $section = '')
    {
        if (!empty($key)) {
            if (is_array($key)) {
                $values = $key;
                $section = $value;
            } else {
                $values = array($key => $value);
            }

            $section = $this->fillEmptySection($section);
            $this->addSection($section);

            foreach ($values as $key => $value) {
                $this->configuration[(string) $section][(string) $key] = (string) $value;
            }
            $isAdded = true;
        } else {
            $isAdded = false;
        }

        return $isAdded;
    }

    public function deleteKey($key, $sectionName = '')
    {
        $section = $this->fillEmptySection($sectionName);
        if (isset($this->configuration[$section][$key])) {
            unset($this->configuration[$section][$key]);
            $isDeleted = true;
        } else {
            $isDeleted = false;
        }

        return $isDeleted;
    }

    public function getValue($key, $sectionName = '')
    {
        $section = $this->fillEmptySection($sectionName);

        if (isset($this->configuration[$section][$key])) {
            $value = $this->configuration[$section][$key];
        } else {
            $value = null;
        }

        return $value;
    }

    public function setValue($key, $value = '', $sectionName = '')
    {
        $isSet = $this->addValue($key, $value, $sectionName);

        return $isSet;
    }

    public function setDefaultSection($sectionName = '')
    {
        if (!empty($sectionName) and in_array($sectionName, $this->sections)) {
            $this->defaultSection = $sectionName;
            $isSet = true;
        } else {
            $isSet = false;
        }

        return $isSet;
    }

    public function getDefaultSection()
    {
        return $this->defaultSection;
    }

    public function Save($file = '')
    {
        $isSaved = false;

        if (!empty($file)) {
            $this->adapter->setFilename($file);
        }

        if ($this->adapter->setConfiguration($this->configuration)) {
            $isSaved = $this->adapter->saveConfiguration();
        }

        return $isSaved;
    }

    public function isSection($sectionName)
    {
        $isSection = in_array($sectionName, $this->sections);

        return $isSection;
    }

    public function isValue($key, $sectionName = '')
    {
        $isValue = isset($this->configuration[$this->fillEmptySection($sectionName)][$key]);

        return $isValue;
    }

    private function fillEmptySection($sectionName = '')
    {
        if (empty($sectionName) and ! empty($this->defaultSection)) {
            $sectionName = $this->defaultSection;
        }

        return $sectionName;
    }
}
