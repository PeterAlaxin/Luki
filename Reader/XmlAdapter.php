<?php
/**
 * XML adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Reader
 * @filesource
 */

namespace Luki\Reader;

class XmlAdapter
{

    private $handle;
    private $totalBytes;
    private $readBytes = 0;
    private $blockSize = 8192;
    private $itemCounter = 0;
    private $block;
    private $blockCounter = 0;
    private $root = null;
    private $start;
    private $nodes;
    private $node = null;
    private $element;
    private $minPosition;
    private $elementName;
    private $endTag;
    private $endTagPosition;

    public function __construct($url)
    {
        $this->open($url);

        unset($url);
    }

    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }

        foreach ($this as &$value) {
            $value = null;
        }
    }

    private function open($url)
    {
        $this->handle = fopen($url, 'r');

        if ($this->handle) {
            $this->setFilesize($url);

            if ($this->totalBytes < $this->blockSize) {
                $this->blockSize = $this->totalBytes;
            }
        }
    }

    private function setFilesize($url)
    {
        $isRemote = empty(@filectime($url));

        if ($isRemote) {
            $headers = get_headers($url, 1);
            $this->totalBytes = $headers['Content-Length'];
        } else {
            $this->totalBytes = filesize($url);
        }
    }

    public function getFilesize()
    {
        return $this->totalBytes;
    }

    public function setBlocksize($newSize)
    {
        $oldSize = $this->blockSize;
        $this->blockSize = (int) $newSize;

        if (empty($this->blockSize)) {
            $this->blockSize = $oldSize;
        }
    }

    public function getBlocksize()
    {
        return $this->blockSize;
    }

    public function getReadBytes()
    {
        return $this->readBytes;
    }

    public function getCounter()
    {
        return $this->itemCounter;
    }

    public function getBlocks()
    {
        return $this->blockCounter;
    }

    public function getItem()
    {
        while (true) {
            if ($this->blockCounter == 0) {
                $this->readBlock();
            }

            if (!isset($this->root)) {
                $this->findRoot();
            }

            $this->findNode();

            if (!is_null($this->node)) {
                break;
            } else {
                $this->readBlock();
            }
        }

        $node = simplexml_load_string($this->node);

        return $node;
    }

    private function readBlock()
    {
        $this->block .= fread($this->handle, $this->blockSize);

        preg_match_all('/<!-- (.*) -->/U', $this->block, $matches);
        foreach ($matches[0] as $match) {
            $this->block = str_replace($match . "\n", '', $this->block);
        }

        $this->readBytes += $this->blockSize;
        if ($this->readBytes >= $this->totalBytes) {
            $this->readBytes = $this->totalBytes;
        }
        $this->blockCounter++;
    }

    private function findRoot()
    {
        preg_match('/<([^>\?]+)>/', $this->block, $matches);

        if (isset($matches[1])) {
            $this->root = $matches[1];
            $this->start = strpos($this->block, $matches[0]) + strlen($matches[0]);
        }
    }

    private function findNode()
    {
        $this->nodes = substr($this->block, $this->start);
        preg_match('/<([^>]+)>/', $this->nodes, $matches);

        if (isset($matches[1])) {
            $this->element = $matches[1];
            $this->findMinPosition();
            $this->findElemendName();
            $this->findEndTagPosition();
            $this->setNode();
        } else {
            $this->node = null;
        }
    }

    private function findMinPosition()
    {
        $found = array();
        $positions = array(
            strpos($this->element, " "),
            strpos($this->element, "\r"),
            strpos($this->element, "\n"),
            strpos($this->element, "\t"));

        foreach ($positions as $position) {
            if ($position !== false) {
                $found[] = $position;
            }
        }

        if ($found === array()) {
            $this->minPosition = false;
        } else {
            $this->minPosition = min($found);
        }
    }

    private function findElemendName()
    {
        if ($this->minPosition !== false && $this->minPosition != 0) {
            $this->elementName = substr($this->element, 0, $this->minPosition);
        } else {
            $this->elementName = $this->element;
        }
    }

    private function findEndTagPosition()
    {
        $this->endTag = "</" . $this->elementName . ">";
        $this->endTagPosition = false;

        $lastCharPosition = strlen($this->element) - 1;
        if (substr($this->element, $lastCharPosition) == "/") {
            $this->endTag = "/>";
            $this->endTagPosition = $lastCharPosition;

            $position = strpos($this->nodes, "<");
            if ($position !== false) {
                $this->endTagPosition += $position + 1;
            }
        }

        if ($this->endTagPosition === false) {
            $this->endTagPosition = strpos($this->nodes, $this->endTag);
        }
    }

    private function setNode()
    {
        if ($this->endTagPosition !== false) {
            $this->node = trim(substr($this->nodes, 0, $this->endTagPosition + strlen($this->endTag)));
            $this->block = substr($this->block, strpos($this->block, $this->endTag) + strlen($this->endTag));
            $this->start = 0;
            $this->itemCounter++;
        } else {
            if ($this->totalBytes == $this->readBytes) {
                $this->node = false;
            } else {
                $this->node = null;
            }
        }
    }
}
