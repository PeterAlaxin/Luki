<?php
/**
 * Image filter adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Image
 * @filesource
 */

namespace Luki\Image\Filter;

use Luki\Image\Filter\BasicInterface;

abstract class BasicAdapter implements BasicInterface
{
    public $arg1;
    public $arg2;
    public $arg3;
    public $arg4;
    public $method;

    public function __construct($arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
        $this->arg4 = $arg4;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function filter($image)
    {
        $tmpImage = $image;

        if (imagefilter($tmpImage, $this->method)) {
            $image = $tmpImage;
        }

        return $image;
    }
}