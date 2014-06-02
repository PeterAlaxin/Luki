<?php

/**
 * Cookie adapter
 *
 * Luki framework
 * Date 19.9.2012
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

namespace Luki\Request;

/**
 * Cookie adapter
 * 
 * @package Luki
 */
class cookieAdapter extends basicAdapter
{

    public function __construct()
    {
        $this->saveInputs($_COOKIE);
    }

}

# End of file