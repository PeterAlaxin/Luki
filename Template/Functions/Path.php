<?php
/**
 * Path template function
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

use Luki\Storage;

class Path
{

    public function Get($route, $parameters = array())
    {
        if (Storage::isSaved('Router')) {
            $path = Storage::Router()->getRoute($route, $parameters);
        } else {
            $path = '';
        }

        return $path;
    }
}