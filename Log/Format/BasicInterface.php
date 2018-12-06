<?php
/**
 * Log Format interface
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

interface BasicInterface
{

    public function __construct($format);

    public function __destruct();

    public function Transform($parameters);
}