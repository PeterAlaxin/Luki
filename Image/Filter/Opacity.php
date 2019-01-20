<?php
/**
 * Opacity filter
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

class Opacity extends BasicAdapter
{
    private $image;

    public function filter($image)
    {
        if ($this->arg1 >= 0 and $this->arg1 <= 100) {
            $this->image = $image;
            imagealphablending($this->image, false);

            if ($this->opacity()) {
                $image = $this->image;
            }
        }

        return $image;
    }

    private function opacity()
    {
        $opacity  = (int) $this->arg1 / 100;
        $minAlpha = $this->minAlpha();
        list($width, $height) = $this->getSize();

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $colorxy = imagecolorat($this->image, $x, $y);
                $alpha   = ( $colorxy >> 24 ) & 0xFF;
                if ($minAlpha !== 127) {
                    $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minAlpha );
                } else {
                    $alpha += 127 * $opacity;
                }
                $alphacolorxy = imagecolorallocatealpha($this->image, ( $colorxy >> 16 ) & 0xFF,
                    ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha);
                if (!imagesetpixel($this->image, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function minAlpha()
    {
        $minAlpha = 127;
        list($width, $height) = $this->getSize();

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $alpha = ( imagecolorat($this->image, $x, $y) >> 24 ) & 0xFF;
                if ($alpha < $minAlpha) {
                    $minAlpha = $alpha;
                }
            }
        }

        return $minAlpha;
    }

    private function getSize()
    {
        return array(imagesx($this->image), imagesy($this->image));
    }
}