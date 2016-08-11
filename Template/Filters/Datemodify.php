<?php
/**
 * Datemodify template filter adapter
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

class Datemodify
{

    public function Get($value, $modifier)
    {
        $date = new \DateTime($value);
        $dateModified = $date->modify($modifier);

        return $dateModified;
    }
}
