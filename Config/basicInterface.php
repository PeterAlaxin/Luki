<?php

/**
 * Config Adapter interface
 *
 * Luki framework
 * Date 19.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Config;

/**
 * Config Adapter interface
 * 
 * @package Luki
 */
interface basicInterface
{

    public function __construct($fileName, $allowCreate);

    public function setFilename($fileName);

    public function getFilename();

    public function getSections();

    public function setConfiguration($configuration);

    public function getConfiguration();

    public function saveConfiguration();

    public function createConfigFile($fileName, $allowCreate);
}

# End of file