<?php

/**
 * Server adapter
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
 * Server adapter
 * 
 * @package Luki
 */
class serverAdapter extends basicAdapter
{

    public function __construct()
    {
        $this->saveInputs($_SERVER);
    }

}

# End of file