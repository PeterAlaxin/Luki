<?php
/**
 * Files adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki\Request;

use Luki\Request\BasicAdapter;

class FilesAdapter extends BasicAdapter
{

    public function __construct()
    {
        $this->saveInputs($_FILES);
    }
}
