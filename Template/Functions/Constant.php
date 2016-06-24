<?php

/**
 * Constant template function 
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Functions;

/**
 * Constant template function
 * 
 * @package Luki
 */
class Constant
{

    public function Get($name)
    {
        $constant = NULL;
        $constants = get_defined_constants(TRUE);
        
        foreach($constants as $group => $groupConstants) {
            foreach($groupConstants as $key => $value) {
                if($key == $name) {
                    $constant = $value;                    
                }
            }
        }
        
        unset($name, $constants, $group, $groupConstants, $key, $value);
        return $constant;
    }

}

# End of file