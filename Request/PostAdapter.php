<?php
/**
 * Post adapter
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
use Luki\Url;

class postAdapter extends BasicAdapter
{

    public function __construct()
    {

        if (empty($_FILES) and ! empty($_POST) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $_SESSION['__post__'] = $_POST;
            Url::Reload($_SERVER['REQUEST_URI']);
        }

        if (!empty($_SESSION['__post__'])) {
            $_POST = $_SESSION['__post__'];
            unset($_SESSION['__post__']);
        }

        $this->saveInputs($_POST);
    }
}