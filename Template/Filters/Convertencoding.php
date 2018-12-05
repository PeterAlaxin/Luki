<?php
/**
 * Convert encoding template filter adapter
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

class Convertencoding
{
    protected $list = array();

    public function __construct()
    {
        $this->list = mb_list_encodings();
    }

    public function Get($value, $from = 'UTF-8', $to = 'ISO-8859-1')
    {
        if (in_array($from, $this->list) and in_array($to, $this->list)) {
            $converted = mb_convert_encoding($value, $to, $from);
        } else {
            $converted = $value;
        }

        return $converted;
    }
}