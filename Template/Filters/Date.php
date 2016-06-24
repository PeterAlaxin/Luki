<?php

/**
 * Date template filter adapter
 *
 * Luki framework
 * Date 22.3.2013
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Template\Filters;

/**
 * Date template filter
 * 
 * @package Luki
 */
class Date
{

    public function Get($value, $format = '%d.%m.%Y', $timeZoneName = '')
    {
        if ( empty($timeZoneName) ) {
            $timeZoneName = date_default_timezone_get();
        }

        $timeZone = new \DateTimeZone($timeZoneName);

        if ( is_a($value, 'DateTime') ) {
            $date = $value;
            $date->setTimezone($timeZone);
        } else {
            $date = new \DateTime($value, $timeZone);
        }

        if(strpos($format, '%') === FALSE) {
            $formatedDate = $date->format($format);
        }
        else {
            $formatedDate = strftime($format, $date->getTimestamp());
        }

        unset($value, $format, $timeZoneName, $date, $timeZone);
        return $formatedDate;
    }

}

# End of file