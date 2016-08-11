<?php
/**
 * MySQL Result Iterator class
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

class MysqlResult extends Result implements \Iterator
{

    function __construct($result)
    {
        $this->result = $result;

        if (is_resource($this->result)) {
            $this->numberOfRecords = mysql_num_rows($this->result);

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
            mysql_data_seek($this->result, $this->position)) {
            $this->row = mysql_fetch_assoc($this->result, MYSQL_ASSOC);
        }
    }
}
