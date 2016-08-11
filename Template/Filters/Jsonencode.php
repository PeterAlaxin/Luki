<?php
/**
 * Jsonencode template filter adapter
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

class Jsonencode
{

    public function Get($value)
    {
        $json = json_encode($value);

        return $json;
    }
}
