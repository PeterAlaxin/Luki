<?php
/**
 * Constant template function
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

namespace Luki\Template\Functions;

class Constant
{

    public function Get($name)
    {
        $constant  = null;
        $constants = get_defined_constants(true);

        foreach ($constants as $group => $groupConstants) {
            foreach ($groupConstants as $key => $value) {
                if ($key == $name) {
                    $constant = $value;
                }
            }
        }

        return $constant;
    }
}