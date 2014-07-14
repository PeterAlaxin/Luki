<?php

/**
 * Select input
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
 * Select input
 * 
 * @package Luki
 */
class Select extends basicFactory
{

    private $_data = array();

    public function setData($data)
    {
        $this->_data = (array) $data;
        
        return $this;
    }

    public function getHtml()
    {
        $html = array( 'label' => $this->prepareLabel(),
          'input' => $this->prepareInput() );

        return $html;
    }

    private function prepareAttributes()
    {
        $attributes = '';
        foreach ( $this->getAttributes() as $attribute => $value ) {
            if ( 'value' !== $attribute ) {
                $attributes .= $attribute . '="' . $value . '" ';
            }
        }

        unset($attribute, $value);
        return $attributes;
    }

    private function prepareLabel()
    {
        $label = '<label for="' . $this->getId() . '">';
        $label .= $this->getLabel();
        $label .= '</label>';

        return $label;
    }

    private function prepareInput()
    {
        $input = '<select ' . $this->prepareAttributes() . '>';
        $selected = $this->getValue();

        foreach ( $this->_data as $value => $option ) {
            $input .= '<option value="' . $value . '"';

            if ( $value == $selected ) {
                $input .= ' selected="selected"';
            }

            $input .= '>' . $option . '</option>';
        }

        $input .= '</select>';

        unset($selected, $value, $option);
        return $input;
    }

}

# End of file