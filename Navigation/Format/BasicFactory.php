<?php
/**
 * Navigation format factory
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Navigation
 * @filesource
 */

namespace Luki\Navigation\Format;

use Luki\Navigation;
use Luki\Navigation\Format\BasicInterface;

abstract class BasicFactory implements BasicInterface
{

    public $format;
    public $navigation = null;

    public function __construct(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    public function Format($options)
    {
        
    }
}
