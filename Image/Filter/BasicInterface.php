<?php
/**
 * Image filter Adapter interface
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Image
 * @filesource
 */

namespace Luki\Image\Filter;

interface BasicInterface
{

    public function __construct($arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null);

    public function __destruct();

    public function filter($image);
}