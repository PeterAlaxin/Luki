<?php
/**
 * Merge template filter adapter
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

class Merge
{

    public function Get($value, $mergeWith)
    {
        if (is_array($value) and is_array($mergeWith)) {
            $merge = array_merge($value, $mergeWith);
        } else {
            $merge = $value;
        }

        return $merge;
    }
}
