<?php
/**
 * Raw template filter adapter
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

class Raw
{

    public function Get($value)
    {
        $raw = rawurlencode($value);

        return $raw;
    }
}
