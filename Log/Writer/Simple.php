<?php

/**
 * Simple Log Writer
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
 * Simple Log Writer
 * 
 * @package Luki
 */
class Simple implements basicInterface
{

    public function __construct($fileName = '')
    {
        unset($fileName);
    }

    public function Write($content)
    {
        if ( is_array($content) ) {
            $content = json_encode($content);
        }

        echo $content . '<br />';

        unset($content);
    }

}

# End of file