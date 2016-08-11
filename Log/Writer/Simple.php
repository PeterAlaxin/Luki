<?php
/**
 * Simple Log Writer
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Log
 * @filesource
 */

namespace Luki\Log\Writer;

use Luki\Log\Writer\BasicFactory;
use Luki\Log\Writer\BasicInterface;

class Simple extends BasicFactory implements BasicInterface
{

    public function Write($content)
    {
        if (is_array($content)) {
            $content = json_encode($content);
        }

        echo $content . '<br />';
    }
}
