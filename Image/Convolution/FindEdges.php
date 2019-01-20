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

class FindEdges extends BasicAdapter
{
    public $matrix = array(
        array(-1, -1, -1),
        array(-2, 8, -1),
        array(-1, -1, -1)
    );

}