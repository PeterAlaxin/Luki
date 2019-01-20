<?php
/**
 * Contrast filter
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

class Contrast extends BasicAdapter
{
    public $method = IMG_FILTER_CONTRAST;

    public function filter($image)
    {
        if ($this->arg1 >= -100 and $this->arg1 <= 100) {
            $tmpImage = $image;
            if (imagefilter($tmpImage, $this->method, $this->arg1)) {
                $image = $tmpImage;
            }
        }

        return $image;
    }
}