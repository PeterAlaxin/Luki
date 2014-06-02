<?php

/**
 * Config class
 *
 * Luki framework
 * Date 19.9.2012
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

namespace Luki;

use Luki\Config\basicInterface;

/**
 * Config class
 *
 * Load configuration
 *
 * @package Luki
 */
class Config
{

    private $_configuration = array();
    private $_configurationAdapter = NULL;
    private $_defaultSection = '';
    private $_sections = array();

    public function __construct(basicInterface $configurationAdapter)
    {
        $this->_configurationAdapter = $configurationAdapter;
        $this->_configuration = $this->_configurationAdapter->getConfiguration();
        $this->_sections = $this->_configurationAdapter->getSections();

        if ( isset($this->_sections[0]) ) {
            $this->_defaultSection = $this->_sections[0];
        }

        unset($configurationAdapter);
    }

    public static function findAdapter($file)
    {
        $filePathInformation = pathinfo($file);
        $adapter = __NAMESPACE__ . '\Config\\' . $filePathInformation['extension'] . 'Adapter';

        unset($filePathInformation);
        return $adapter;
    }

    public function getConfiguration()
    {
        return $this->_configuration;
    }

    public function getConfigurationFile()
    {
        return $this->_configurationAdapter->getFilename();
    }

    public function addSection($section, $values = array())
    {
        $isAdded = FALSE;

        if ( !empty($section) and is_string($section) and ! in_array($section, $this->_sections) ) {
            $this->_configuration[$section] = array();
            $this->_sections[] = $section;
            $this->setDefaultSection($section);
            $isAdded = TRUE;

            if ( !empty($values) and is_array($values) ) {
                $this->addValue($values);
            }
        }

        unset($section, $values);
        return $isAdded;
    }

    public function deleteSection($section)
    {
        $isDeleted = FALSE;
        $section = $this->_fillEmptySection($section);

        if ( in_array($section, $this->_sections) ) {
            unset($this->_configuration[$section]);
            $this->_sections = array_keys($this->_configuration);
            $isDeleted = TRUE;
        }

        unset($section);
        return $isDeleted;
    }

    public function getSection($section)
    {
        $section = $this->_fillEmptySection($section);
        $values = array();

        if ( in_array($section, $this->_sections) ) {
            $values = $this->_configuration[$section];
        }

        unset($section);
        return $values;
    }

    public function getSections()
    {
        return $this->_sections;
    }

    public function addValue($key, $value = '', $section = '')
    {
        $isAdded = FALSE;

        if ( !empty($key) ) {
            if ( is_array($key) ) {
                $values = $key;
                $section = $value;
            } else {
                $values = array( $key => $value );
            }

            $section = $this->_fillEmptySection($section);
            $this->addSection($section);

            foreach ( $values as $key => $value ) {
                $this->_configuration[(string) $section][(string) $key] = (string) $value;
            }
            $isAdded = TRUE;
        }

        unset($key, $value, $section, $values);
        return $isAdded;
    }

    public function deleteKey($key, $section = '')
    {
        $isDeleted = FALSE;

        $section = $this->_fillEmptySection($section);
        if ( isset($this->_configuration[$section][$key]) ) {
            unset($this->_configuration[$section][$key]);
            $isDeleted = TRUE;
        }

        unset($key, $section);
        return $isDeleted;
    }

    public function getValue($key, $section = '')
    {
        $section = $this->_fillEmptySection($section);
        $value = NULL;

        if ( isset($this->_configuration[$section][$key]) ) {
            $value = $this->_configuration[$section][$key];
        }

        unset($key, $section);
        return $value;
    }

    public function setValue($key, $value = '', $section = '')
    {
        $isSet = $this->addValue($key, $value, $section);

        unset($key, $value, $section);
        return $isSet;
    }

    public function setDefaultSection($section = '')
    {
        $isSet = FALSE;

        if ( !empty($section) and in_array($section, $this->_sections) ) {
            $this->_defaultSection = $section;
            $isSet = TRUE;
        }

        unset($section);
        return $isSet;
    }

    public function getDefaultSection()
    {
        return $this->_defaultSection;
    }

    public function Save($file = '')
    {
        $isSaved = FALSE;

        if ( !empty($file) ) {
            $this->_configurationAdapter->setFilename($file);
        }

        if ( $this->_configurationAdapter->setConfiguration($this->_configuration) ) {
            $isSaved = $this->_configurationAdapter->saveConfiguration();
        }

        unset($file);
        return $isSaved;
    }

    private function _fillEmptySection($section = '')
    {
        if ( empty($section) and ! empty($this->_defaultSection) ) {
            $section = $this->_defaultSection;
        }

        return $section;
    }

}

# End of file