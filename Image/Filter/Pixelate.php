<?php
/**
 * Pixelate filter
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

use Luki\Image\Filter\BasicAdapter;

class Pixelate extends BasicAdapter
{
    public $method = IMG_FILTER_PIXELATE;

    public function filter($image)
    {
        if ($this->arg1 > 0) {
            $mode     = !empty($this->arg2);
            $tmpImage = $image;
            if (imagefilter($tmpImage, $this->method, (int) $this->arg1, $mode)) {
                $image = $tmpImage;
            }
        }

        return $image;
    }
}