<?php
/**
 * MySQLi Result Iterator class
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

use Luki\Data\MySQL\Result;

class MysqliResult extends Result implements \Iterator
{

    function __construct($result)
    {
        $this->result = $result;

        if (is_object($this->result)) {
            $this->numberOfRecords = mysqli_num_rows($this->result);

            if (!empty($this->numberOfRecords)) {
                $this->rewind();
            }
        }
    }

    public function _setRecord()
    {
        $this->row = false;

        if ($this->position >= 0 and
            $this->position < $this->numberOfRecords and
            mysqli_data_seek($this->result, $this->position)) {
            $this->row = mysqli_fetch_array($this->result, MYSQLI_ASSOC);
        }
    }
}
