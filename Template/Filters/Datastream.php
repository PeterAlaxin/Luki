<?php
/**
 * Datastream template filter adapter
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

use Luki\Image;

class Datastream
{

    public function Get($value)
    {
        $image = new Image($value);
        $type = $image->getType();

        if (!empty($type)) {
            $stream = 'data:' . $type . ';base64,' . base64_encode(file_get_contents($value));
        } else {
            $stream = $value;
        }

        return $stream;
    }
}
