<?php
/**
 * Radio input
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

class Radio extends BasicFactory
{
    private $data       = array();
    private $labelClass = '';

    public function setData($data)
    {
        $this->data = (array) $data;

        return $this;
    }

    public function getHtml()
    {
        $html = array(
            'inputs' => $this->prepareInputs(),
            'value'  => $this->getValue(),
            'hint'   => $this->getHint(),
            'name'   => $this->getName()
        );

        return $html;
    }

    public function setLabelClass($labelClass)
    {

        $this->labelClass = $labelClass;

        return $this;
    }

    private function prepareInputs()
    {
        $inputs = array();
        $key    = 0;
        foreach ($this->data as $value => $name) {
            $inputs[] = array(
                'input' => $this->prepareInput($key, $value),
                'label' => $this->prepareLabel($key, $name),
            );
            $key ++;
        }

        return $inputs;
    }

    private function prepareLabel($key, $name)
    {
        $label = '<label for="'.$this->getId().'_'.$key.'"';
        if (!empty($this->labelClass)) {
            $label .= ' class="'.$this->labelClass.'"';
        }
        $label .= '>'.$name;
        $label .= '</label>';

        return $label;
    }

    private function prepareInput($key, $value)
    {
        $input = '<input type="radio" id="'.$this->getId().'_'.$key.'" name="'.$this->getName().'" '.$this->prepareAttributes();
        $input .= ' value="'.$value.'"';
        if ($value == $this->getValue()) {
            $input .= ' checked="checked"';
        }
        $input .= '>';

        return $input;
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
}