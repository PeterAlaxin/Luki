<?php

/**
 * Hidden input
 *
 * Luki framework
 * Date 30.5.2014
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Formular
 * @filesource
 */

namespace Luki\Formular;

use Luki\Formular\basicFactory;

/**
 * Hidden input
 * 
 * @package Luki
 */
class Hidden extends basicFactory
{

    public function getHtml()
    {
        $html = parent::getHtml();
        $html['label'] = '';

        return $html;
    }

}

# End of file