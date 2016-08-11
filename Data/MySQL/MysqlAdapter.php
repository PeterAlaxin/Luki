<?php
/**
 * MySQL data adapter
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

namespace Luki\Data\MySQL;

use Luki\Data\MySQL\BasicAdapter;
use Luki\Data\MySQL\Select;
use Luki\Data\MySQL\MysqlResult as Result;
use Luki\Exception\DataException;
use Luki\Storage;
use Luki\Time;

class MysqlAdapter extends BasicAdapter
{

    public $connection = null;

    public function __construct($options)
    {
        $this->connection = mysql_connect($options['server'], $options['user'], $options['password']);

        if (!isset($this->connection) or false === $this->connection) {
            throw new DataException('MySQL connection error');
        }

        if (!mysql_select_db($options['database'], $this->connection)) {
            throw new DataException('MySQL connection error');
        }

        $this->Query('SET CHARACTER_SET_CONNECTION=' . $options['coding'] . ';');
        $this->Query('SET CHARACTER_SET_CLIENT=' . $options['coding'] . ';');
        $this->Query('SET CHARACTER_SET_RESULTS=' . $options['coding'] . ';');
        $this->Query('SET NAMES ' . $options['coding'] . ';');
        $this->Query('SET lc_time_names = "' . $options['locale'] . '";');
    }

    public function __destruct()
    {
        mysql_close($this->connection);
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function Select()
    {
        $select = new Select($this);

        return $select;
    }

    public function Query($sql)
    {
        if (Storage::isProfiler()) {
            Time::stopwatchStart('Luki_Data_MySQL_MySQL');
        }

        $result = mysql_query((string) $sql, $this->connection);

        if (Storage::isProfiler()) {
            $time = Time::getStopwatch('Luki_Data_MySQL_MySQL');
            Storage::Profiler()->Add('Data', array('sql' => (string) $sql, 'time' => $time));
        }

        if (is_resource($result)) {
            $result = new Result($result);
        }

        return $result;
    }

    public function escapeString($string)
    {
        $string = mysql_real_escape_string($string, $this->connection);

        return $string;
    }

    public function saveLastID($table)
    {
        $this->lastID = mysql_insert_id($this->connection);
        $this->allLlastID[$table] = $this->lastID;
    }

    public function saveUpdated($table)
    {
        $this->updated = mysql_affected_rows($this->connection);
        $this->allUpdated[$table] = $this->updated;
    }

    public function saveDeleted($table)
    {
        $this->deleted = mysql_affected_rows($this->connection);
        $this->allDeleted[$table] = $this->deleted;
    }
}
