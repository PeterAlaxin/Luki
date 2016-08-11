<?php
/**
 * Blank template test 
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

class Blank
{

    public function Is($value)
    {
        $isBlank = empty($value);

        return $isBlank;
    }
}
