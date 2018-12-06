<?php
/**
 * Log format factory
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

namespace Luki\Log\Format;

use Luki\Log\Format\BasicInterface;

abstract class BasicFactory implements BasicInterface
{
    public $format = '';

    public function __construct($format = '')
    {
        $this->format = $format;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Transform($parameters)
    {

    }
}