<?php
/**
 * File Log Writer
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

class File extends BasicFactory implements BasicInterface
{

    public function Write($content)
    {
        if (is_array($content)) {
            $content = json_encode($content);
        }

        file_put_contents($this->file, $content.PHP_EOL, FILE_APPEND);
    }
}