<?php
/**
 * Translate template filter adapter
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

use Luki\Storage;

class Tran
{

    public function Get($original, $language, $section = '')
    {
        $tran = Storage::Language()->Get($original, $language, $section);

        return $tran;
    }
}