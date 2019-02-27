<?php
/**
 * Checkbox input
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

class Checkbox extends BasicFactory
{

    public function __construct($name, $label, $placeholder = '')
    {
        parent::__construct($name, $label, $placeholder);
        $this->setType('checkbox');
    }

    public function setValue($value)
    {
        if ('off' === $value or false === $value or 0 === $value or "0" === $value or null === $value) {
            $this->value = 0;
        } else {
            $this->value = 1;
        }

        if ($this->value) {
            $this->setAttribute('checked', 'checked');
        }
        return $this;
    }
}