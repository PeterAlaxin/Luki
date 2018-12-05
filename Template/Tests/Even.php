<?php
/**
 * Even template test
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

class Even
{

    public function Is($value)
    {
        $isEven = (floor($value / 2) == ($value / 2));

        return $isEven;
    }
}