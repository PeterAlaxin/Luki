<?php

/**
 * Range template function 
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Functions;

/**
 * Range template function
 * 
 * @package Luki
 */
class Range {

	public function Get($nBegin, $nEnd, $nStep = 1)
	{
        $aRange = range($nBegin, $nEnd, $nStep);
                
        unset($nBegin, $nEnd, $nStep);
        return $aRange;
	}
}

# End of file