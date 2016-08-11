<?php
/**
 * Html template filter adapter
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

class Html
{

    public function Get($value)
    {
        $html = html_entity_decode($value);

        return $html;
    }
}
