<?php

/**
 * Language class
 *
 * Luki framework
 * Date 16.12.2012
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

use Luki\Config;

/**
 * Language class
 *
 * @package Luki
 */
class Language
{

    private $_languagesPath = NULL;
    private $_languages = array();

    public function __construct($name, $file)
    {
        $this->_languagesPath = dirname($file);
        $this->addToLanguages($name, $file);

        unset($name, $file);
    }

    public function Get($text, $key = '', $sectionName = '')
    {
        $translation = NULL;

        if ( !empty($key) ) {
            $translation = $this->_languages[$key]->getValue($text, $sectionName);
        } else {
            foreach ( $this->_languages as $language ) {
                $translation = $language->getValue($text, $sectionName);

                if ( !empty($translation) ) {
                    break;
                }
            }
        }

        unset($text, $key, $sectionName, $language);
        return $translation;
    }

    public function Find($text)
    {
        $translation = NULL;

        foreach ( $this->_languages as $language ) {
            $sections = $language->getSections();

            foreach ( $sections as $section ) {
                $translation = $language->getValue($text, $section);

                if ( !empty($translation) ) {
                    break;
                }
            }

            if ( !empty($translation) ) {
                break;
            }
        }

        unset($text, $language, $sections, $section);
        return $translation;
    }

    public function addToLanguages($name, $file)
    {
        $adapterName = Config::findAdapter($file);

        if ( is_file($file) ) {
            $adapter = new $adapterName($file);
        } elseif ( is_file($this->_languagesPath . PATH_SEPARATOR . $file) ) {
            $adapter = new $adapterName($this->_languagesPath . PATH_SEPARATOR . $file);
        }

        $this->_languages[$name] = new Config($adapter);

        unset($name, $file);
        return $this;
    }

    public function getSection($name, $sectionName = '')
    {
        $section = $this->_languages[$name]->getSection($sectionName);

        unset($name, $sectionName);
        return $section;
    }

    public function setSection($name, $sectionName)
    {
        $isSet = $this->_languages[$name]->setDefaultSection($sectionName);

        unset($name, $sectionName);
        return $isSet;
    }

    public function getSections($sectionName)
    {
        $sections = $this->_languages[$sectionName]->getSections();

        unset($sectionName);
        return $sections;
    }

    public function getPath()
    {
        return $this->_languagesPath;
    }

    public function setPath($newPath)
    {
        $oldPath = $this->_languagesPath;

        if ( is_dir($newPath) ) {
            $this->_languagesPath = $newPath;
        } else {
            $oldPath = FALSE;
        }

        unset($newPath);
        return $oldPath;
    }

    public static function getPreferdLanguage()
    {
        $languages = self::getAllowedLanguages();

        foreach ( $languages as $prefered => $version ) {
            if ( !empty($prefered) ) {
                break;
            }
        }

        unset($languages, $version);
        return $prefered;
    }

    public static function getAllowedLanguages()
    {
        $languages = array();
        $acceptLanguages = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
        
        if ( isset($acceptLanguages) ) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguages, $parsed);

            if ( !empty($parsed[1]) and ! empty($parsed[4]) ) {
                $languages = array_combine($parsed[1], $parsed[4]);

                foreach ( $languages as $language => $version ) {
                    if ( empty($version) or ' ' == $version ) {
                        $languages[$language] = '1.0';
                    }
                }

                arsort($languages, SORT_NUMERIC);
            }
        }

        unset($parsed, $language, $version, $acceptLanguages);
        return $languages;
    }

}

# End of file