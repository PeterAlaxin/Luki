<?php
/**
 * DateTime input
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

class DateTime extends BasicFactory
{

    public function __construct($name, $label, $placeholder = '')
    {
        parent::__construct($name, $label, $placeholder);
        $this->setType('datetime-local');
    }

    public function setValue($value)
    {
        $this->value = date("Y-m-d\TH:i:s", strtotime($value));
        $this->setAttribute('value', $this->getValue());

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}