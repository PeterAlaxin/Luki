<?php
/**
 * Image convolution adapter
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

namespace Luki\Image\Convolution;

use Luki\Image\Convolution\BasicInterface;

abstract class BasicAdapter implements BasicInterface
{
    public $matrix;
    public $divisor;
    public $offset;

    public function __construct()
    {
        $this->divisor = array_sum(array_map('array_sum', $this->matrix));
        $this->offset  = 0;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function convolution($image)
    {
        $tmpImage = $image;

        if (imageconvolution($tmpImage, $this->matrix, $this->divisor, $this->offset)) {
            $image = $tmpImage;
        }

        return $image;
    }

    public function setDivisor($divisor)
    {
        $this->divisor = $divisor;

        return $this;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }
}