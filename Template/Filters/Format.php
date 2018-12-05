<?php
/**
 * Format template filter adapter
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

class Format
{

    public function Get($value, $par1 = null, $par2 = null, $par3 = null, $par4 = null, $par5 = null, $par6 = null,
                        $par7 = null, $par8 = null, $par9 = null, $par10 = null)
    {
        $function = '$format = sprintf($value';

        for ($i = 1; $i < 11; $i++) {
            eval('$par = is_null($par'.$i.');');

            if (!$par) {
                $function .= ', $par'.$i;
            }
        }

        $function .= ');';

        eval($function);

        return $format;
    }
}