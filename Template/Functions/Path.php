<?php

/**
 * Path template function 
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

use Luki\Storage;

/**
 * Path template function
 * 
 * @package Luki
 */
class Path
{

    public function Get($route, $parameters=array())
    {
        $path = '';
        if(Storage::isSaved('Router')) {
            $path = Storage::Router()->getRoute($route, $parameters);
        }
        
        unset($route, $parameters);
        return $path;
    }

}