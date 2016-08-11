<?php
/**
 * Log Writer interface
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

namespace Luki\Log\Writer;

interface BasicInterface
{

    public function __construct($file);

    public function __destruct();

    public function Write($content);
}
