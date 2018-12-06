<?php
/**
 * Xml Log Format adapter
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

class Xml extends BasicFactory implements BasicInterface
{

    public function Transform($parameters)
    {
        $content = array();

        foreach ($parameters as $key => $value) {
            $content[$key] = $value;
        }

        return $content;
    }
}