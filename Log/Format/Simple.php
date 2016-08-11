<?php
/**
 * Simple Log Format adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Log
 * @filesource
 */

namespace Luki\Log\Format;

use Luki\Log\Format\BasicFactory;
use Luki\Log\Format\BasicInterface;

class Simple extends BasicFactory implements BasicInterface
{

    public function __construct($format = '')
    {
        if (empty($format)) {
            $format = '%timestamp%: %priority% (%priorityValue%): %message%';
        }

        parent::__construct($format);
    }

    public function Transform($parameters)
    {
        $content = $this->format;

        foreach ($parameters as $key => $value) {
            $content = preg_replace('/%' . $key . '%/', $value, $content);
        }

        return $content;
    }
}
