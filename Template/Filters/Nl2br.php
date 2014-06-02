<?php

/**
 * Nl2br template filter adapter
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

namespace Luki\Template\Filters;

/**
 * Nl2br template filter
 * 
 * @package Luki
 */
class Nl2br
{

    public function Get($value)
    {
        $nlbr = nl2br($value, TRUE);

        unset($value);
        return $nlbr;
    }

}

# End of file