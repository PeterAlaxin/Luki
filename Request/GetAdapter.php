<?php
/**
 * Get adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Request
 * @filesource
 */

namespace Luki\Request;

use Luki\Request\BasicAdapter;

class GetAdapter extends BasicAdapter
{

    public function __construct()
    {
        $this->saveInputs($_GET);
    }
}