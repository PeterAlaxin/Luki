<?php
/**
 * Message class
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

class Message
{
    private $stream;
    private $uid;
    private $header = null;
    private $body   = array();

    public function __construct($stream, $uid = 0)
    {
        $this->stream = $stream;
        $this->uid    = $uid;
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function makeHeader($info)
    {
        $header = array(
            'date'     => $info->date,
            'subject'  => $info->subject,
            'to'       => array(
                'name'  => trim($info->toaddress),
                'email' => $info->to[0]->mailbox.'@'.$info->to[0]->host
            ),
            'from'     => array(
                'name'  => trim($info->fromaddress),
                'email' => $info->from[0]->mailbox.'@'.$info->from[0]->host
            ),
            'reply-to' => array(
                'name'  => trim($info->reply_toaddress),
                'email' => $info->reply_to[0]->mailbox.'@'.$info->reply_to[0]->host
            ),
            'recent'   => $info->Recent,
            'unseen'   => $info->Unseen,
            'flagged'  => $info->Flagged,
            'answered' => $info->Answered,
            'deleted'  => $info->Deleted,
            'draft'    => $info->Draft,
            'id'       => (int) $info->Msgno,
            'size'     => $info->Size,
        );

        return $header;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getId()
    {
        return imap_msgno($this->stream, $this->uid);
    }

    public function getHeader()
    {
        if (empty($this->header)) {
            $this->header = self::makeHeader(imap_headerinfo($this->stream, $this->getId()));
        }

        return $this->header;
    }

    public function getBody()
    {
        if (empty($this->body)) {
            $structure = imap_fetchstructure($this->stream, $this->uid, FT_UID);
            if ($structure->type == TYPEMULTIPART) {
                foreach ($structure->parts as $key => $part) {
                    $this->body[] = array(
                        'type'    => $part->subtype,
                        'content' => imap_fetchbody($this->stream, $this->uid, $key, FT_UID)
                    );
                }
            } else {
                $content = imap_body($this->stream, $this->uid, FT_UID);
                if (false !== base64_decode($content, true)) {
                    $content = imap_base64($content);
                }
                $this->body[] = array(
                    'type'    => 'PLAIN',
                    'content' => $content
                );
            }
        }

        return $this->body;
    }

    public function isDeleted()
    {
        if (empty($this->header)) {
            $this->getHeader();
        }

        return ($this->header['deleted'] == 'D');
    }

    public function delete()
    {
        return $this->setFlag('Deleted');
    }

    public function undelete()
    {
        return $this->clearFlag('Deleted');
    }

    public function isUnseen()
    {
        if (empty($this->header)) {
            $this->getHeader();
        }

        return ($this->header['recent'] == 'N' or $this->header['unseen'] == 'U');
    }

    public function seen()
    {
        return $this->setFlag('Seen');
    }

    public function unseen()
    {
        return $this->clearFlag('Seen');
    }

    public function isFlagged()
    {
        if (empty($this->header)) {
            $this->getHeader();
        }

        return ($this->header['flagged'] == 'F');
    }

    public function flag()
    {
        return $this->setFlag('Flagged');
    }

    public function unflag()
    {
        return $this->clearFlag('Flagged');
    }

    private function setFlag($flag)
    {
        $success = imap_setflag_full($this->stream, $this->getId(), '\\'.$flag);

        if ($success) {
            $this->header = null;
        }

        return $success;
    }

    private function clearFlag($flag)
    {
        $success = imap_clearflag_full($this->stream, $this->getId(), '\\'.$flag);

        if ($success) {
            $this->header = null;
        }

        return $success;
    }

    public function moveTo($folder)
    {
        return imap_mail_copy($this->stream, $this->uid, $folder, CP_UID | CP_MOVE);
    }
}