<?php
/**
 * Imagesrc template filter adapter
 * 
 * Designed for use with other components - do not applicable separately
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

class Imagesrc
{

    public function Get($name, $width, $height, $id, $quality = 80, $crop = 0, $background = 'ffffff', $transparent = 0, $wattermark = 0)
    {
        $param = implode('_', [$width, $height, $id, $quality, $crop, $background, $transparent, $wattermark]);
        $string = Url::makeLink($name).'_'.base64_encode($param);

        return $string;
    }
}