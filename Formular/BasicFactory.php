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
    public $value       = null;
    private $attributes = array();
    private $errors     = array();
    private $hint       = '';
    private $id         = '';
    private $label      = '';
    private $name       = '';
    private $required   = false;
    private $type       = '';
    private $validators = array();

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

    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint()
    {
        return $this->hint;
    }

    public function setRequired()
    {
        $this->required               = true;
        $this->attributes['required'] = '';

        return $this;
    }

    public function setPlaceholder($placeholder)
    {
        $this->attributes['placeholder'] = $placeholder;

        return $this;
    }

    public function addValidator(\Luki\Validator\BasicFactory $validator)
    {
        if (strpos(get_class($validator), 'NotBlank') > 0) {
            $this->addToAttribute('required', '');
        }
        $this->validators[] = $validator;

        return $this;
    }

    public function isValid()
    {
        $this->errors = array();

        foreach ($this->validators as $validator) {
            if (!$validator->isValid($this->value)) {
                $this->errors[] = $validator->getError();
            }
        }

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
            if ('' == $value) {
                $attributes .= $attribute.' ';
            } else {
                $attributes .= $attribute.'="'.$value.'" ';
            }
        }

        $html = array(
            'label'     => '<label for="'.$this->getId().'" class="control-label">'.$this->getLabel().'</label>',
            'labelText' => $this->getLabel(),
            'input'     => '<input '.$attributes.'>',
            'value'     => $this->getValue(),
            'id'        => $this->id,
            'hint'      => $this->getHint(),
            'type'      => $this->getType(),
            'name'      => $this->getName()
        );

        return $html;
    }

    public function disabled()
    {
        $this->attributes['disabled'] = 'disabled';

        return $this;
    }

    public function readonly()
    {
        $this->attributes['readonly'] = 'readonly';

        return $this;
    }

    public function setAutofocus()
    {
        $this->attributes['autofocus'] = '';

        return $this;
    }
}