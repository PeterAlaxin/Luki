<?php
/**
 * Select input
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

use Luki\Formular\BasicFactory;

class Select extends BasicFactory
{
    private $data       = array();
    private $values     = array();
    private $validators = array();
    private $errors     = array();

    public function setData($data)
    {
        $this->data = (array) $data;

        return $this;
    }

    public function getHtml()
    {
        $html = array(
            'label' => $this->prepareLabel(),
            'input' => $this->prepareInput(),
            'value' => $this->getValue(),
            'hint'  => $this->getHint(),
            'name'  => $this->getName()
        );

        return $html;
    }

    private function prepareAttributes()
    {
        $attributes = '';
        foreach ($this->getAttributes() as $attribute => $value) {
            if ('value' !== $attribute) {
                $attributes .= $attribute.'="'.$value.'" ';
            }
        }

        return $attributes;
    }

    private function prepareLabel()
    {
        $label = '<label for="'.$this->getId().'">';
        $label .= $this->getLabel();
        $label .= '</label>';

        return $label;
    }

    private function prepareInput()
    {
        $input = '<select '.$this->prepareAttributes().'>';

        foreach ($this->data as $value => $option) {
            if (is_array($option)) {
                $input .= '<optgroup label="'.$value.'">';
                foreach ($option as $subvalue => $suboption) {
                    $input .= '<option value="'.$subvalue.'"';
                    if (in_array($subvalue, $this->values)) {
                        $input .= ' selected="selected"';
                    }
                    $input .= '>'.$suboption.'</option>';
                }
                $input .= '</optgroup>';
            } else {
                $input .= '<option value="'.$value.'"';
                if (in_array($value, $this->values)) {
                    $input .= ' selected="selected"';
                }
                $input .= '>'.$option.'</option>';
            }
        }

        $input .= '</select>';

        return $input;
    }

    public function setValue($value)
    {
        $this->values[] = $value;

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
            foreach ($this->values as $value) {
                if (!$validator->isValid($value)) {
                    $this->errors[] = $validator->getError();
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}