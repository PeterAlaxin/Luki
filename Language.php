<?php
/**
 * Language class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Language
 * @filesource
 */

namespace Luki;

use Luki\Config;

class Language
{
    private $languagesPath = null;
    private $languages     = array();

    public function __construct($name, $file)
    {
        $this->languagesPath = dirname($file);
        $this->addToLanguages($name, $file);
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Get($text, $key = '', $sectionName = '')
    {
        $translation = null;

        if (!empty($key)) {
            $translation = $this->languages[$key]->getValue($text, $sectionName);
        } else {
            foreach ($this->languages as $language) {
                $translation = $language->getValue($text, $sectionName);

                if (!empty($translation)) {
                    break;
                }
            }
        }

        return $translation;
    }

    public function Find($text)
    {
        $translation = null;

        foreach ($this->languages as $language) {
            $sections = $language->getSections();

            foreach ($sections as $section) {
                $translation = $language->getValue($text, $section);

                if (!empty($translation)) {
                    break;
                }
            }

            if (!empty($translation)) {
                break;
            }
        }

        return $translation;
    }

    public function addToLanguages($name, $file)
    {
        $adapterName = Config::findAdapter($file);

        if (is_file($file)) {
            $adapter = new $adapterName($file);
        } elseif (is_file($this->languagesPath.PATH_SEPARATOR.$file)) {
            $adapter = new $adapterName($this->languagesPath.PATH_SEPARATOR.$file);
        }

        $this->languages[$name] = new Config($adapter);

        return $this;
    }

    public function getSection($name, $sectionName = '')
    {
        $section = $this->languages[$name]->getSection($sectionName);

        return $section;
    }

    public function setSection($name, $sectionName)
    {
        $isSet = $this->languages[$name]->setDefaultSection($sectionName);

        return $isSet;
    }

    public function getSections($sectionName)
    {
        $sections = $this->languages[$sectionName]->getSections();

        return $sections;
    }

    public function getPath()
    {
        return $this->languagesPath;
    }

    public function setPath($newPath)
    {
        $oldPath = $this->languagesPath;

        if (is_dir($newPath)) {
            $this->languagesPath = $newPath;
        } else {
            $oldPath = false;
        }

        return $oldPath;
    }

    public static function getPreferdLanguage()
    {
        $languages = self::getAllowedLanguages();

        foreach ($languages as $prefered => $version) {
            if (!empty($prefered)) {
                break;
            }
        }

        return $prefered;
    }

    public static function getAllowedLanguages()
    {
        $languages       = array();
        $acceptLanguages = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');

        if (isset($acceptLanguages)) {
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguages, $parsed);

            if (!empty($parsed[1]) and ! empty($parsed[4])) {
                $languages = array_combine($parsed[1], $parsed[4]);

                foreach ($languages as $language => $version) {
                    if (empty($version) or ' ' == $version) {
                        $languages[$language] = '1.0';
                    }
                }

                arsort($languages, SORT_NUMERIC);
            }
        }

        return $languages;
    }
}