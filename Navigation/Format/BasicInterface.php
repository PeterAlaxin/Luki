<?php
/**
 * Navigation Format interface
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Navigation
 * @filesource
 */

namespace Luki\Navigation\Format;

interface BasicInterface
{

    public function __construct($navigation);

    public function __destruct();

    public function setFormat($format);

    public function Format($options);
}
