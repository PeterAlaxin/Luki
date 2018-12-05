<?php
/**
 * Defined template test
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

class Defined
{

    public function Is($value)
    {
        $isDefined = !is_null($value);

        return $isDefined;
    }
}