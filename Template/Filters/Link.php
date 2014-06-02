<?php

/**
 * Link template filter adapter
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

use Luki\Url;

/**
 * Link template filter
 * 
 * @package Luki
 */
class Link
{

    public function Get($value)
    {
        $link = Url::makeLink($value);

        unset($value);
        return $link;
    }

}

# End of file