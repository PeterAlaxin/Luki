<?php

/**
 * Select input
 *
 * Luki framework
 * Date 30.5.2014
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
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
    private $_value = array();

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

        foreach ( $this->_data as $value => $option ) {
            $input .= '<option value="' . $value . '"';

            if ( in_array($value, $this->_value ) ) {
                $input .= ' selected="selected"';
            }

            $input .= '>' . $option . '</option>';
        }

        $input .= '</select>';

        unset($value, $option);
        return $input;
    }

    public function setValue($value)
    {
        $this->_value[] = $value;

        unset($value);
        return $this;
    }

    public function isValid()
    {
        $this->_validate();

        $isValid = FALSE;
        if ( empty($this->_errors) ) {
            $isValid = TRUE;
        }

        return $isValid;
    }

    private function _validate()
    {
        $this->_errors = array();

        foreach ( $this->_validators as $validator ) {
            foreach($this->_value as $value) {
                if ( !$validator->isValid($value) ) {
                    $this->_errors[] = $validator->getError();
                }
            }
        }

        unset($validator);
    }

}

# End of file