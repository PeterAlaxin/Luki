<?php
/**
 * Data Adapter interface
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Data
 * @filesource
 */

namespace Luki\Data;

interface BasicInterface
{

    public function __construct($options);

    public function __destruct();

    public function Query($sql);

    public function Select();

    public function Insert($table, $values);

    public function Update($table, $values, $where);

    public function Delete($table, $where);

    public function getLastID($table);

    public function getUpdated($table);

    public function getDeleted($table);

    public function escapeString($string);

    public function saveLastID($table);

    public function saveUpdated($table);

    public function saveDeleted($table);

    public function getStructure($table);
}