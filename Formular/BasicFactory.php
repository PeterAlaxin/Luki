<?php
/**
 * Formular input factory
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Formular
 * @filesource
 */

namespace Luki\Formular;

use Luki\Formular\BasicInterface;

abstract class BasicFactory implements BasicInterface
{
    private $name       = '';
    private $id         = '';
    private $type       = '';
    private $value      = null;
    private $attributes = array();
    private $label      = '';
    private $validators = array();
    private $errors     = array();
    private $required   = false;

    public function __construct($name, $label, $placeholder = '')
    {
        $this->name = $name;
        $this->setAttribute('name', $this->getName());

        $this->id = $name.'_id';
        $this->setAttribute('id', $this->getId());

        $this->setLabel($label);
        $this->setPlaceholder($placeholder);

        return $this;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function addToAttribute($attribute, $value)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            $this->attributes[$attribute] .= ' '.$value;
        } else {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    public function deleteAttribute($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            unset($this->attributes[$attribute]);
        }

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        if (in_array($type,
                array('button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local', 'email', 'file', 'hidden', 'image',
                'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time', 'url',
                'week'))) {
            $this->type = $type;
            $this->setAttribute('type', $this->getType());
        }

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setValue($value)
    {
        $this->value = $value;
        $this->setAttribute('value', $this->getValue());

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setRequired()
    {
        $this->required               = true;
        $this->attributes['required'] = 'required';

        return $this;
    }

    public function setPlaceholder($placeholder)
    {
        $this->attributes['placeholder'] = $placeholder;

        return $this;
    }

    public function addValidator(\Luki\Validator\BasicFactory $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    public function isValid()
    {
        $this->_validate();

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getHtml()
    {
        $attributes = '';
        foreach ($this->getAttributes() as $attribute => $value) {
            $attributes .= $attribute.'="'.$value.'" ';
        }

        $html = array(
            'label' => '<label for="'.$this->getId().'">'.$this->getLabel().'</label>',
            'input' => '<input '.$attributes.'>',
            'value' => $this->getValue()
        );

        return $html;
    }

    private function _validate()
    {
        $this->errors = array();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($this->value)) {
                $this->errors[] = $validator->getError();
            }
        }
    }
}