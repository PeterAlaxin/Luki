<?php
/**
 * Config Adapter interface
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Config
 * @filesource
 */

namespace Luki\Config;

interface BasicInterface
{

    public function __construct($fileName, $allowCreate);

    public function __destruct();

    public function setFilename($fileName);

    public function getFilename();

    public function getSections();

    public function setConfiguration($configuration);

    public function getConfiguration();

    public function saveConfiguration();

    public function createConfigFile($fileName, $allowCreate);
}