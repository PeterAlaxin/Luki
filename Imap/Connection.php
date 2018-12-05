<?php
/**
 * Connection class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Imap
 * @filesource
 */

namespace Luki\Imap;

class Connection
{
    private $server;
    private $port;
    private $user;
    private $password;

    public function __construct($server, $port, $user, $password)
    {
        $this->server   = $server;
        $this->port     = $port;
        $this->user     = $user;
        $this->password = $password;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }
}