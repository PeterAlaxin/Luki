<?php

/**
 * MySQLi data adapter
 *
 * Luki framework
 * Date 9.12.2012
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

namespace Luki\Data\MySQL;

use Luki\Data\MySQL\basicAdapter;
use Luki\Data\MySQL\Select as Select;
use Luki\Data\MySQL\mysqliResult as Result;
use Luki\Storage;
use Luki\Time;

/**
 * MySQLi data adapter
 * 
 * @package Luki
 */
class mysqliAdapter extends basicAdapter
{

    public $mySql = NULL;

    public function __construct($options)
    {
        $this->mySql = new \mysqli(
                $options['server'], $options['user'], $options['password'], $options['database']);

        if ( !empty($this->mySql->connect_error) ) {
            echo 'Connection error: ' . $this->mySql->connect_error;
            exit;
        }

        $this->Query('SET CHARACTER_SET_CONNECTION=' . $options['coding'] . ';');
        $this->Query('SET CHARACTER_SET_CLIENT=' . $options['coding'] . ';');
        $this->Query('SET CHARACTER_SET_RESULTS=' . $options['coding'] . ';');

        unset($options);
    }

    public function __destruct()
    {
        $this->mySql->close();
    }

    public function Select()
    {
        $select = new Select($this);

        return $select;
    }

    public function Query($sql)
    {
        if ( Storage::isProfiler() ) {
            Time::stopwatchStart('Luki_Data_MySQL_MySQLi');
        }

        $result = $this->mySql->query((string) $sql);

        if ( Storage::isProfiler() ) {
            $time = Time::getStopwatch('Luki_Data_MySQL_MySQLi');
            Storage::Profiler()->Add('Data', array( 'sql' => (string) $sql, 'time' => $time ));
        }

        if ( is_a($result, 'mysqli_result') ) {
            $result = new Result($result);
        }

        unset($sql, $time);
        return $result;
    }

    public function escapeString($string)
    {
        $string = $this->mySql->real_escape_string($string);

        return $string;
    }

    public function saveLastID($table)
    {
        $this->lastID = $this->mySql->insert_id;
        $this->allLlastID[$table] = $this->lastID;
    }

    public function saveUpdated($table)
    {
        $this->updated = $this->mySql->affected_rows;
        $this->allUpdated[$table] = $this->updated;
    }

    public function saveDeleted($table)
    {
        $this->deleted = $this->mySql->affected_rows;
        $this->allDeleted[$table] = $this->deleted;
    }

}

# End of file