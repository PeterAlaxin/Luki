<?php
/**
 * Config YML adapter
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

class YmlAdapter extends BasicAdapter
{

    public function __construct($fileName, $allowCreate = false)
    {
        parent::__construct($fileName, $allowCreate);

        $this->configuration = yaml_parse(file_get_contents($this->fileName));
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $isSaved = $this->saveToFile(yaml_emit($this->configuration));

        return $isSaved;
    }
}