<?php

/**
 * Textarea input
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
 * Textarea input
 * 
 * @package Luki
 */
class Textarea extends basicFactory
{

    public function getHtml()
    {
        $attributes = '';
        foreach ( $this->getAttributes() as $attribute => $value ) {
            if ( 'value' !== $attribute ) {
                $attributes .= $attribute . '="' . $value . '" ';
            }
        }

        $html = array( 'label' => '<label for="' . $this->getId() . '">' . $this->getLabel() . '</label>',
          'input' => '<textarea ' . $attributes . '>' . $this->getValue() . '</textarea>' );

        unset($attributes, $attribute, $value);
        return $html;
    }

}

# End of file