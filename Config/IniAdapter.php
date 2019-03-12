<?php
/**
 * Config INI adapter
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

use Luki\Config\BasicAdapter;

class IniAdapter extends BasicAdapter
{
    private $folder;

    public function __construct($fileName, $allowCreate = false)
    {
        parent::__construct($fileName, $allowCreate);

        $this->saveFolder();
        $this->openFile();
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $content = '';
        foreach ($this->configuration as $section => $values) {
            $content .= '['.$section.']'.chr(10);

            foreach ($values as $key => $value) {
                $content .= $key.' = "'.$value.'"'.chr(10);
            }

            $content .= chr(10);
        }

        $isSaved = $this->saveToFile($content);

        return $isSaved;
    }

    private function saveFolder()
    {
        $info         = pathinfo($this->fileName);
        $this->folder = $info['dirname'].'/';
    }

    private function openFile()
    {
        $ini = file_get_contents($this->fileName);
        preg_match_all('/; import "(.+)"/', $ini, $match);

        foreach ($match[1] as $key => $item) {
            $file = $this->folder.$item;
            if (is_file($file) and is_readable($file)) {
                $ini = str_replace($match[0][$key], file_get_contents($file), $ini);
            }
        }

        $this->configuration = parse_ini_string($ini, true);
    }
}