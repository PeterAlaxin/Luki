<?php

/**
 * Config yml adapter
 *
 * Luki framework
 * Date 6.7.2013
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

namespace Luki\Config;

use Luki\Config\basicAdapter;

/**
 * Config yml adapter
 * 
 * @package Luki
 */
class ymlAdapter extends basicAdapter
{

    public function __construct($fileName, $allowCreate = FALSE)
    {
        parent::__construct($fileName, $allowCreate);

        $this->configuration = yaml_parse(file_get_contents($this->fileName));

        unset($fileName, $allowCreate);
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $isSaved = $this->saveToFile(yaml_emit($this->configuration));

        return $isSaved;
    }

}

# End of file