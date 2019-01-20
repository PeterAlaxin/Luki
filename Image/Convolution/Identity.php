<?php
/**
 * Emboss filter
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

use Luki\Image\Convolution\BasicAdapter;

class Identity extends BasicAdapter
{
    public $matrix = array(
        array(0, 0, 0),
        array(0, 1, 0),
        array(0, 0, 0)
    );

}