<?php
/**
 * Divisibleby template test
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

class Divisibleby
{

    public function Is($value, $divider)
    {
        $isDivisible = (floor($value / $divider) == ($value / $divider));

        return $isDivisible;
    }
}