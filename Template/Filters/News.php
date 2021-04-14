<?php
/**
 * News template filter adapter
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

class News
{

    public function Get($value)
    {
        $from = [' a ', ' do ', ' i ', ' k ', ' ku ', ' na ', ' nad ', ' od ', ' oproti ', ' pod ', ' pred ', ' pri ', ' s ', ' so ', ' v ', ' vo ', ' z ', ' za ', ' zo ', ' A ', ' Do ', ' I ', ' K ', ' Ku ', ' Na ', ' Nad ', ' Od ', ' Oproti ', ' Pod ', ' Pred ', ' Pri ', ' S ', ' So ', ' V ', ' Vo ', ' Z ', ' Za ', ' Zo '];
        $to   = [' a&nbsp;', ' do&nbsp;', ' i&nbsp;', ' k&nbsp;', ' ku&nbsp;', ' na&nbsp;', ' nad&nbsp;', ' od&nbsp;', ' oproti&nbsp;', ' pod&nbsp;', ' pred&nbsp;', ' pri&nbsp;', ' s&nbsp;', ' so&nbsp;', ' v&nbsp;', ' vo&nbsp;', ' z&nbsp;', ' za&nbsp;', ' zo&nbsp;', ' A&nbsp;', ' Do&nbsp;', ' I&nbsp;', ' K&nbsp;', ' Ku&nbsp;', ' Na&nbsp;', ' Nad&nbsp;', ' Od&nbsp;', ' Oproti&nbsp;', ' Pod&nbsp;', ' Pred&nbsp;', ' Pri&nbsp;', ' S&nbsp;', ' So&nbsp;', ' V&nbsp;', ' Vo&nbsp;', ' Z&nbsp;', ' Za&nbsp;', ' Zo&nbsp;'];

        $news = str_replace($from, $to, $value);

        return $news;
    }
}
