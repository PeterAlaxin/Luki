<?php

/**
 * Post adapter
 *
 * Luki framework
 * Date 19.9.2012
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

namespace Luki\Request;

use Luki\Url;

/**
 * Post adapter
 * 
 * @package Luki
 */
class postAdapter extends basicAdapter
{

    public function __construct()
    {
        if(!empty($_POST)) {
            $_SESSION['__post__'] = $_POST;
            Url::Reload($_SERVER['REQUEST_URI']);
        }
        
        if(!empty($_SESSION['__post__'])) {
            $_POST = $_SESSION['__post__'];
            unset($_SESSION['__post__']);
        }
        
        $this->saveInputs($_POST);
    }

}

# End of file