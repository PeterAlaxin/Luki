<?php
/**
 * Image convolution Adapter interface
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

namespace Luki\Image\Convolution;

interface BasicInterface
{

    public function __construct();

    public function __destruct();

    public function convolution($image);

    public function setDivisor($divisor);

    public function setOffset($offset);
}