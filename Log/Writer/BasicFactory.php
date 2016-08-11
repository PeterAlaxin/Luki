<?php
/**
 * Log writer factory
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

use Luki\Log\Writer\BasicInterface;

abstract class BasicFactory implements BasicInterface
{

    public $file = null;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Write($content)
    {
        
    }
}
