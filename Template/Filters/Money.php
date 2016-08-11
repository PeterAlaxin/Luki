<?php
/**
 * Money template filter adapter
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

class Money
{

    public function Get($value, $format = null)
    {
        if (!$this->isWindows()) {
            if (empty($format)) {
                $money = money_format('%!n', (float) $value);
            } elseif ('eur' == $format) {
                $money = money_format('%!n&nbsp;€', (float) $value);
            } else {
                $money = money_format($format, (float) $value);
            }
        } else {
            $money = number_format((float) $value, 2, ',', '.');

            if ('eur' == $format) {
                $money .= '&nbsp;€';
            }
        }

        return $money;
    }

    private function isWindows()
    {
        $isWindows = false;

        if (!empty($_SERVER["HTTP_USER_AGENT"])) {
            if (0 === preg_match('/windows/i', $_SERVER["HTTP_USER_AGENT"])) {
                $isWindows = true;
            }
        }

        return $isWindows;
    }
}
