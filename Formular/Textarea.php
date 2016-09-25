<?php
/**
 * Textarea input
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

class Textarea extends BasicFactory
{

    public function getHtml()
    {
        $attributes = '';
        foreach ($this->getAttributes() as $attribute => $value) {
            if ('value' !== $attribute) {
                $attributes .= $attribute . '="' . $value . '" ';
            }
        }

        $html = array(
			'label' => '<label for="' . $this->getId() . '">' . $this->getLabel() . '</label>',
            'input' => '<textarea ' . $attributes . '>' . $this->getValue() . '</textarea>',
			'value' => $this->getValue()
			);

        return $html;
    }
}
