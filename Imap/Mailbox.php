<?php
/**
 * Mailbox class
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

use Luki\Imap\Message;

class Mailbox
{
    private $folder;
    private $stream;

    public function __construct(\Luki\Imap\Connection $connection, $folder)
    {
        $this->stream = imap_open('{'.$connection->getServer().':'.$connection->getPort().'/novalidate-cert}'.$folder,
            $connection->getUser(), $connection->getPassword());

        if (!is_resource($this->stream)) {
            throw new \Exception("Connection failed to mailbox $folder failed!");
        }

        $this->folder = $folder;
    }

    public function __destruct()
    {
        if (is_resource($this->stream)) {
            imap_close($this->stream);
        }

        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getInfo()
    {
        $check = imap_mailboxmsginfo($this->stream);
        $info  = array(
            'folder'   => $this->folder,
            'messages' => $check->Nmsgs,
            'recent'   => $check->Recent,
            'unread'   => $check->Unread,
            'deleted'  => $check->Deleted,
            'size'     => $check->Size
        );

        return $info;
    }

    public function getMessages()
    {
        $messages = array();
        for ($key = 1; $key <= imap_num_msg($this->stream); $key++) {
            $messages[imap_uid($this->stream, $key)] = Message::makeHeader(imap_headerinfo($this->stream, $key));
        }

        return $messages;
    }

    public function getMessage($uid)
    {
        $message = new Message($this->stream, $uid);

        return $message;
    }
}