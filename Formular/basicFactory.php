<?php

/**
 * Formular input factory
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

use Luki\Formular\basicInterface;

/**
 * Formular input factory
 * 
 * @abstract
 * @package Luki
 */
abstract class basicFactory implements basicInterface
{

    private $_name = '';
    private $_id = '';
    private $_type = '';
    private $_value = NULL;
    private $_attributes = array();
    private $_label = '';
    private $_validators = array();
    private $_errors = array();
    private $_required = FALSE;

    public function __construct($name, $label, $placeholder = '')
    {
        $this->_name = $name;
        $this->setAttribute('name', $this->getName());

        $this->_id = $name . '_id';
        $this->setAttribute('id', $this->getId());

        $this->setLabel($label);
        $this->setPlaceholder($placeholder);

        unset($name, $label, $placeholder);
        return $this;
    }

    public function setAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;

        unset($attribute, $value);
        return $this;
    }

    public function addToAttribute($attribute, $value)
    {
        if ( array_key_exists($attribute, $this->_attributes) ) {
            $this->_attributes[$attribute] .= ' ' . $value;
        } else {
            $this->setAttribute($attribute, $value);
        }

        unset($attribute, $value);
        return $this;
    }

    public function deleteAttribute($attribute)
    {
        if ( array_key_exists($attribute, $this->_attributes) ) {
            unset($this->_attributes[$attribute]);
        }

        unset($attribute);
        return $this;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setType($type)
    {
        if ( in_array($type, array( 'button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time', 'url', 'week' )) ) {
            $this->_type = $type;
            $this->setAttribute('type', $this->getType());
        }

        unset($type);
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setValue($value)
    {
        $this->_value = $value;
        $this->setAttribute('value', $this->getValue());

        unset($value);
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function setLabel($label)
    {
        $this->_label = $label;

        unset($label);
        return $this;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function setRequired()
    {
        $this->_required = TRUE;
        $this->_attributes['required'] = 'required';
        
        return $this;
    }

    public function setPlaceholder($placeholder)
    {
        $this->_attributes['placeholder'] = $placeholder;

        unset($placeholder);
        return $this;
    }

    public function addValidator(\Luki\Validator\basicFactory $validator)
    {
        $this->_validators[] = $validator;

        unset($validator);
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

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getHtml()
    {
        $attributes = '';
        foreach ( $this->getAttributes() as $attribute => $value ) {
            $attributes .= $attribute . '="' . $value . '" ';
        }

        $html = array( 'label' => '<label for="' . $this->getId() . '">' . $this->getLabel() . '</label>',
          'input' => '<input ' . $attributes . '>' );

        unset($attributes, $attribute, $value);
        return $html;
    }

    private function _validate()
    {
        $this->_errors = array();

        foreach ( $this->_validators as $validator ) {
            if ( !$validator->isValid($this->_value) ) {
                $this->_errors[] = $validator->getError();
            }
        }

        unset($validator);
    }

}

# End of file