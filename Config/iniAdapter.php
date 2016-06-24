<?php

/**
 * Config ini adapter
 *
 * Luki framework
 * Date 19.9.2012
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
 * Config ini adapter
 * 
 * @package Luki
 */
class iniAdapter extends basicAdapter
{

    public function __construct($fileName, $allowCreate = FALSE)
    {
        parent::__construct($fileName, $allowCreate);

        $this->configuration = parse_ini_file($this->fileName, TRUE);

        unset($fileName, $allowCreate);
    }

    public function saveConfiguration()
    {
        parent::saveConfiguration();

        $content = '';

        foreach ( $this->configuration as $section => $values ) {
            $content .= '[' . $section . ']' . chr(10);

            foreach ( $values as $key => $value ) {
                $content .= $key . ' = "' . $value . '"' . chr(10);
            }

            $content .= chr(10);
        }

        $isSaved = $this->saveToFile($content);

        unset($content, $section, $values, $key, $value);
        return $isSaved;
    }

}

# End of file