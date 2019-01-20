<?php
/**
 * Smooth filter
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

class Smooth extends BasicAdapter
{
    public $method = IMG_FILTER_SMOOTH;

    public function filter($image)
    {
        if ($this->arg1 >= -8 and $this->arg1 <= 8) {
            $tmpImage = $image;
            if (imagefilter($tmpImage, $this->method, $this->arg1)) {
                $image = $tmpImage;
            }
        }

        return $image;
    }
}