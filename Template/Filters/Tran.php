<?php

/**
 * Translate template filter adapter
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

namespace Luki\Template\Filters;

use Luki\Storage;

/**
 * Translate template filter
 * 
 * @package Luki
 */
class Tran
{

    public function Get($original, $language, $section = '')
    {
        $tran = Storage::Language()->Get($original, $language, $section);

        unset($original, $language, $section);
        return $tran;
    }

}

# End of file