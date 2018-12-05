<?php
/**
 * Constant template test
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

class Constant
{

    public function Is($value)
    {
        $isConstant = defined($value);

        return $isConstant;
    }
}