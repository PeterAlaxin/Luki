<?php
/**
 * Odd template test 
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template\Tests;

class Odd
{

    public function Is($value)
    {
        $isOdd = !(floor($value / 2) == ($value / 2));

        return $isOdd;
    }
}
