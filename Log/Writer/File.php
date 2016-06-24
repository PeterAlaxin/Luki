<?php

/**
 * File Log Writer
 *
 * Luki framework
 * Date 16.12.2012
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

namespace Luki\Log\Writer;

use Luki\Log\Writer\basicInterface;

/**
 * File Log Writer
 * 
 * @package Luki
 */
class File implements basicInterface
{

    private $_fileName = NULL;

    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function Write($content)
    {
        if ( is_array($content) ) {
            $content = json_encode($content);
        }

        file_put_contents($this->_fileName, $content . PHP_EOL, FILE_APPEND);

        unset($content);
    }

}

# End of file