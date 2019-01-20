<?php
/**
 * Colorize filter
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

class Colorize extends BasicAdapter
{
    public $method = IMG_FILTER_COLORIZE;

    public function filter($image)
    {
        if ($this->testColors()) {
            $tmpImage = $image;

            if (empty($this->arg4)) {
                $result = imagefilter($tmpImage, $this->method, (int) $this->arg1, (int) $this->arg2, (int) $this->arg3);
            } else {
                $result = imagefilter($tmpImage, $this->method, (int) $this->arg1, (int) $this->arg2, (int) $this->arg3,
                    (int) $this->arg4);
            }

            if ($result) {
                $image = $tmpImage;
            }
        }

        return $image;
    }

    private function testColors()
    {
        $red   = ($this->arg1 >= -255 and $this->arg1 <= 255);
        $green = ($this->arg2 >= -255 and $this->arg2 <= 255);
        $blue  = ($this->arg3 >= -255 and $this->arg3 <= 255);

        return ($red and $green and $blue);
    }
}