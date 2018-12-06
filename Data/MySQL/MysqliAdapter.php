<?php
/**
 * MySQLi data adapter
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
use Luki\Data\MySQL\MysqliResult as Result;
use Luki\Exception\DataException;
use Luki\Storage;
use Luki\Time;

class MysqliAdapter extends BasicAdapter
{
    public $mySql = null;

    public function __construct($options)
    {
        $this->mySql = new \mysqli($options['server'], $options['user'], $options['password'], $options['database']);

        if (!empty($this->mySql->connect_error)) {
            throw new DataException('MySQL connection error');
        }

        $this->Query('SET CHARACTER_SET_CONNECTION='.$options['coding'].';');
        $this->Query('SET CHARACTER_SET_CLIENT='.$options['coding'].';');
        $this->Query('SET CHARACTER_SET_RESULTS='.$options['coding'].';');
        $this->Query('SET NAMES '.$options['coding'].';');
        $this->Query('SET lc_time_names = "'.$options['locale'].'";');
    }

    public function __destruct()
    {
        $this->mySql->close();
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
            Time::stopwatchStart('Luki_Data_MySQL_MySQLi');
        }

        $result = $this->mySql->query((string) $sql);

        if (false === $result and Storage::isLog()) {
            Storage::Log()->Error($this->mySql->error.' in "'.(string) $sql.'"');
        }

        if (Storage::isProfiler()) {
            $time = Time::getStopwatch('Luki_Data_MySQL_MySQLi');
            Storage::Profiler()->Add('Data', array('sql' => (string) $sql, 'time' => $time));
        }

        if (is_a($result, 'mysqli_result')) {
            $result = new Result($result);
        }

        return $result;
    }

    public function escapeString($string)
    {
        $string = $this->mySql->real_escape_string($string);

        return $string;
    }

    public function saveLastID($table)
    {
        $this->lastID             = $this->mySql->insert_id;
        $this->allLlastID[$table] = $this->lastID;
    }

    public function saveUpdated($table)
    {
        $this->updated            = $this->mySql->affected_rows;
        $this->allUpdated[$table] = $this->updated;
    }

    public function saveDeleted($table)
    {
        $this->deleted            = $this->mySql->affected_rows;
        $this->allDeleted[$table] = $this->deleted;
    }
}