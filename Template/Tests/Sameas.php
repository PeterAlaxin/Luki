<?php
/**
 * Sameas template test 
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

class Sameas
{

    public function Is($value, $controll)
    {
        $isSameAs = ($value === $controll);

        return $isSameAs;
    }
}
