<?php

/**
 * Datastream template filter adapter
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

use Luki\Image;
use Luki\Storage;

/**
 * Datastream template filter
 * 
 * @package Luki
 */
class Datastream
{

    public function Get($value)
    {       
        $image = new Image($value);
        $type = $image->getType();
        
        if(!empty($type)) {
            $stream = 'data:' .  $type . ';base64,' . base64_encode(file_get_contents($value));
        }
        else {
            $stream = $value;
        }
                
        unset($value, $image, $type);
        return $stream;
    }

}
