<?php
/**
 * Nl2br template filter adapter
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

class Nl2br
{

    public function Get($value)
    {
        $nlbr = nl2br($value, true);

        return $nlbr;
    }
}
