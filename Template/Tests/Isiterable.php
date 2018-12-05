<?php
/**
 * Iterable template test
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

class Isiterable
{

    public function Is($value)
    {
        $isIterable = is_array($value);

        return $isIterable;
    }
}