<?php

/**
 * MySQL Result Iterator class
 *
 * Luki framework
 * Date 9.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Data\MySQL;

use Luki\Data\MySQL\Result as Result;

/**
 * MySQL Result Iterator class
 *
 * @package Luki
 */
class mysqlResult extends Result implements \Iterator
{

    function __construct($result)
    {
        $this->result = $result;

        if ( is_resource($this->result) ) {
            $this->numberOfRecords = mysql_num_rows($this->result);

            if ( !empty($this->numberOfRecords) ) {
                $this->rewind();
            }
        }

        unset($result);
    }

    public function _setRecord()
    {
        $this->row = FALSE;

        if ( $this->position >= 0 and
                $this->position < $this->numberOfRecords and
                mysql_data_seek($this->result, $this->position) ) {
            $this->row = mysql_fetch_assoc($this->result, MYSQL_ASSOC);
        }
    }

}

# End of file