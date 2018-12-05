<?php
/**
 * Imap class
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

namespace Luki;

use Luki\Imap\Connection;
use Luki\Imap\Mailbox;

class Imap
{
    private $connection;
    private $folders   = array();
    private $master;
    private $protected = array('INBOX', 'Trash');

    public function __construct($server, $port, $user, $password)
    {
        $this->connection = new Connection($server, $port, $user, $password);
        $this->openMailbox("INBOX");
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function openMailbox($name)
    {
        if (!array_key_exists($name, $this->folders)) {
            $this->folders[$name] = new Mailbox($this->connection, $name);
        }

        $this->master = $this->folders[$name];
    }

    public function getAlerts()
    {
        return imap_alerts();
    }

    public function getErrors()
    {
        return imap_errors();
    }

    public function clear()
    {
        return imap_expunge($this->master->getStream());
    }

    public function getMailboxes($pattern = '*')
    {
        $mailboxes = array();
        $list      = imap_getmailboxes($this->master->getStream(), '{'.$this->connection->getServer().'}', $pattern);
        if (is_array($list)) {
            foreach ($list as $key => $item) {
                $utf7Name    = imap_utf7_decode($item->name);
                $mailboxes[] = array(
                    'key'        => $key,
                    'name'       => str_replace('{'.$this->connection->getServer().'}', '', $utf7Name),
                    'delimiter'  => $item->delimiter,
                    'attributes' => $item->attributes
                );
            }
        }

        return $mailboxes;
    }

    public function getMailboxInfo()
    {
        return $this->master->getInfo();
    }

    public function getMessages()
    {
        return $this->master->getMessages();
    }

    public function getMessage($uid)
    {
        return $this->master->getMessage($uid);
    }

    public function createMailbox($name)
    {
        if (in_array($name, $this->protected)) {
            return false;
        }

        $stream  = $this->master->getStream();
        $newName = imap_utf7_encode('{'.$this->connection->getServer().'}'.$name);

        $success = imap_createmailbox($stream, $newName);
        if ($success) {
            imap_subscribe($stream, $newName);
        }

        return $success;
    }

    public function renameMailbox($from, $to)
    {
        if (in_array($from, $this->protected) or in_array($to, $this->protected)) {
            return false;
        }

        $stream  = $this->master->getStream();
        $oldName = imap_utf7_encode('{'.$this->connection->getServer().'}'.$from);
        $newName = imap_utf7_encode('{'.$this->connection->getServer().'}'.$to);

        $success = imap_renamemailbox($stream, $oldName, $newName);
        if ($success) {
            imap_subscribe($stream, $newName);
        }

        return $success;
    }

    public function deleteMailbox($name)
    {
        if (in_array($name, $this->protected)) {
            return false;
        }

        $deletedName = imap_utf7_encode('{'.$this->connection->getServer().'}'.$name);

        $success = imap_deletemailbox($this->master->getStream(), $deletedName);

        return $success;
    }
}