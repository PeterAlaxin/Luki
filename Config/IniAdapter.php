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

    public function __construct($fileName, $allowCreate = false)
    {
        parent::__construct($fileName, $allowCreate);

        $this->configuration = parse_ini_file($this->fileName, true);
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $content = '';
        foreach ($this->configuration as $section => $values) {
            $content .= '[' . $section . ']' . chr(10);

            foreach ($values as $key => $value) {
                $content .= $key . ' = "' . $value . '"' . chr(10);
            }

            $content .= chr(10);
        }

        $isSaved = $this->saveToFile($content);

        return $isSaved;
    }
}
