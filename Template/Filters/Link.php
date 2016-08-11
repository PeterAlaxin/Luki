<?php
/**
 * Link template filter adapter
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

use Luki\Url;

class Link
{

    public function Get($value)
    {
        $link = Url::makeLink($value);

        return $link;
    }
}
