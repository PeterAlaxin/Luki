<?php
/**
 * Striptags template filter adapter
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

namespace Luki\Template\Filters;

class Striptags
{

    public function Get($value)
    {
        $stripped = strip_tags($value);

        return $stripped;
    }
}
