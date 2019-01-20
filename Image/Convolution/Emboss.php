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

class Emboss extends BasicAdapter
{
    public $matrix = array(
        array(-2, -1, 0),
        array(-1, 1, 1),
        array(0, 1, 2)
    );

    public function __construct()
    {
        parent::__construct();

        $this->offset = 127;
    }
}